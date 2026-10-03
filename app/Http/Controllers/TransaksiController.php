<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Pembeli;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    // 1️⃣ Input Barang Masuk
    public function syncAllHargaBarang()
    {
        // Ambil semua barang
        $barangs = DB::table('barang')->get();

        foreach ($barangs as $barang) {
            // Cari harga terbaru dari tabel harga_barang
            $latest = DB::table('harga_barang')
                ->where('barang_id', $barang->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            if ($latest) {
                // Update harga barang
                DB::table('barang')
                    ->where('id', $barang->id)
                    ->update([
                        'harga_beli' => $latest->harga_beli,
                        'harga_jual' => $latest->harga_jual
                    ]);
            } else {
                // Jika tidak ada harga di harga_barang
                DB::table('barang')
                    ->where('id', $barang->id)
                    ->update([
                        'harga_beli' => 0,
                        'harga_jual' => 0
                    ]);
            }
        }

        return response()->json([
            'message' => 'Sinkronisasi semua harga barang berhasil!'
        ]);
    }
    public function rekap(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $this->syncAllHargaBarang();
        // 🔹 1. Hitung stok aktual berdasarkan selisih barang_masuk dan detail_penjualan
        // 🔹 Subquery total barang masuk
        $barangMasukSummary = DB::table('barang_masuk')
            ->select('barang_id', DB::raw('SUM(jumlah) as total_masuk'))
            ->groupBy('barang_id');

        // 🔹 Subquery total barang keluar (dari detail_penjualan)
        $barangKeluarSummary = DB::table('detail_penjualan')
            ->select('barang_id', DB::raw('SUM(jumlah) as total_keluar'))
            ->groupBy('barang_id');

        // 🔹 Gabungkan ke tabel barang
        $stokAktual1 = DB::table('barang as b')
            ->leftJoinSub($barangMasukSummary, 'bm', function ($join) {
                $join->on('b.id', '=', 'bm.barang_id');
            })
            ->leftJoinSub($barangKeluarSummary, 'dp', function ($join) {
                $join->on('b.id', '=', 'dp.barang_id');
            })
            ->select(
                'b.id',
                'b.nama_barang',
                DB::raw('COALESCE(bm.total_masuk, 0) as total_masuk'),
                DB::raw('COALESCE(dp.total_keluar, 0) as total_keluar'),
                DB::raw('(COALESCE(bm.total_masuk, 0) - COALESCE(dp.total_keluar, 0)) as stok_baru')
            )
            ->orderBy('b.nama_barang');

        $stokAktual =    $stokAktual1->get();

        // 🔹 2. Update stok di tabel barang berdasarkan hasil perhitungan
        foreach ($stokAktual as $item) {
            DB::table('barang')
                ->where('id', $item->id)
                ->update(['stok' => $item->stok_baru]);
        }

        // 🔹 3. Lanjutkan rekap penjualan (summary bulanan)
        $query = DB::table('detail_penjualan as d')
            ->join('penjualan as p', 'p.id', '=', 'd.penjualan_id')
            ->join('barang as b', 'b.id', '=', 'd.barang_id')
            ->select(
                DB::raw('YEAR(p.created_at) as tahun'),
                DB::raw('MONTH(p.created_at) as bulan'),
                DB::raw('SUM(d.jumlah * d.harga_jual) as total_penjualan'),
                DB::raw('SUM(d.jumlah) as total_barang_keluar'),
                DB::raw('SUM((d.harga_jual - d.harga_beli) * d.jumlah) as total_margin'),
                DB::raw('SUM(b.stok) as total_sisa_barang'),
                DB::raw('SUM(b.stok * b.harga_beli) as total_harga_tersisa')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc');

        if ($month && $year) {
            $query->whereYear('p.created_at', $year)
                ->whereMonth('p.created_at', $month);
        }

        $rekap = $query->get();

        // 🔹 4. Tambahan data pendukung
        $totalModal = DB::table('permodalan')->sum('jumlah_modal');
        $totalStokSemuaBarang = DB::table('barang')->sum('stok');

        // 🔹 5. Ambil stok per item (setelah update stok)
        $stokPerItem = DB::table('barang')
            ->select(
                'id',
                'nama_barang',
                'stok',
                'harga_beli',
                DB::raw('(stok * harga_beli) as total_nilai')
            )
            ->orderBy('nama_barang')
            ->get();

        // 🔹 6. Gabungkan hasil ke dalam rekap
        $rekap = $rekap->map(function ($item) use ($totalModal, $totalStokSemuaBarang, $stokPerItem) {
            $item->total_modal = $totalModal;
            $item->total_stok_semua_barang = $totalStokSemuaBarang;
            $item->stok_per_item = $stokPerItem;
            return $item;
        });
        Log::info('SQL Rekap:', [
            'sql' => $stokAktual1->toSql(),
            'bindings' => $stokAktual1->getBindings(),
        ]);
        return response()->json([
            'message' => 'Rekap penjualan berhasil diambil dan stok barang diperbarui',
            'data' => $rekap,
        ]);
    }




    public function penjualanStore(Request $request)
    {
        $request->validate([
            'pembeli_id' => 'required|integer|exists:pembeli,id',
            'tgl_transaksi' => 'required|date',
        ]);

        $penjualan = Penjualan::create([
            'pembeli_id' => $request->pembeli_id,
            'tgl_transaksi' => $request->tgl_transaksi,
            'total_harga' => 0,
            'total_modal' => 0,
            'total_margin' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil dibuat',
            'penjualan_id' => $penjualan->id,
            'data' => $penjualan
        ], 201);
    }
    public function detailStore(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $margin = ($request->harga_jual - $request->harga_beli) * $request->jumlah;

            $detail = DetailPenjualan::create([
                'penjualan_id' => $request->penjualan_id,
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_jual' => $request->harga_jual,
                'harga_beli' => $request->harga_beli,
                'margin' => $margin,
            ]);

            // Update stok barang
            DB::table('barang')->where('id', $request->barang_id)
                ->decrement('stok', $request->jumlah);

            // Update total di tabel penjualan
            $penjualan = Penjualan::find($request->penjualan_id);
            $penjualan->increment('total_harga', $request->harga_jual * $request->jumlah);
            $penjualan->increment('total_modal', $request->harga_beli * $request->jumlah);
            $penjualan->increment('total_margin', $margin);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail penjualan berhasil ditambahkan',
                'data' => $detail
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function barangMasuk(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
        ]);
        DB::transaction(function () use ($validated) {
            BarangMasuk::create([
                'barang_id' => $validated['barang_id'],
                'jumlah' => $validated['jumlah'],
                'tanggal_masuk' => now(),
            ]);

            // update stok
            $barang = Barang::find($validated['barang_id']);
            $barang->stok += $validated['jumlah'];
            $barang->save();
        });
        return response()->json(['message' => 'Barang masuk berhasil disimpan.']);
    }

    // 2️⃣ Input Data Pembeli
    public function storePembeli(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'alamat' => 'nullable|string',
            'no_telepon' => 'nullable|string',
        ]);

        $pembeli = Pembeli::create($validated);
        return response()->json($pembeli);
    }

    // 3️⃣ Input Barang Terjual
    public function penjualan(Request $request)
    {
        $validated = $request->validate([
            'pembeli_id' => 'required|exists:pembeli,id',
            'barang' => 'required|array',
            'barang.*.id' => 'required|exists:barang,id',
            'barang.*.jumlah' => 'required|integer|min:1',
        ]);

        $penjualan = DB::transaction(function () use ($validated) {
            $total = 0;
            $marginTotal = 0;

            $penjualan = Penjualan::create([
                'pembeli_id' => $validated['pembeli_id'],
                'tanggal' => now(),
                'total' => 0,
                'margin_total' => 0,
            ]);

            foreach ($validated['barang'] as $item) {
                $barang = Barang::find($item['id']);

                if ($barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok barang {$barang->nama_barang} tidak cukup");
                }

                $subtotal = $barang->harga_jual * $item['jumlah'];
                $margin = ($barang->harga_jual - $barang->harga_beli) * $item['jumlah'];

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                    'margin' => $margin,
                ]);

                $barang->stok -= $item['jumlah'];
                $barang->save();

                $total += $subtotal;
                $marginTotal += $margin;
            }

            $penjualan->update([
                'total' => $total,
                'margin_total' => $marginTotal,
            ]);

            return $penjualan;
        });

        return response()->json(['message' => 'Penjualan berhasil', 'penjualan' => $penjualan]);
    }

    // 4️⃣ Laporan Penjualan dan Stok
    public function laporan()
    {
        $laporan = Penjualan::with(['pembeli', 'details.barang'])->orderBy('tanggal', 'desc')->get();
        return response()->json($laporan);
    }

    // 5️⃣ Cek stok habis
    public function cekStok()
    {
        $stokHabis = Barang::where('stok', '<=', 0)->get();

        if ($stokHabis->isEmpty()) {
            return response()->json(['message' => 'Semua stok aman.']);
        }

        return response()->json([
            'message' => 'Ada barang yang stoknya habis!',
            'data' => $stokHabis
        ]);
    }
}
