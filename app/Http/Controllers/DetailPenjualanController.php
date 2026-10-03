<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailPenjualan;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DetailPenjualanController extends Controller
{

    public function index(Request $request)
    {
        //$details = DetailPenjualan::with(['barang', 'penjualan'])->get();
        $query = DetailPenjualan::with(['barang', 'penjualan']);

        // Jika ada parameter pembeli_id di request, filter berdasarkan itu
        if ($request->filled('penjualan_id')) {
            $penjualan_id = $request->penjualan_id;

            $query->whereHas('penjualan', function ($q) use ($penjualan_id) {
                $q->where('penjualan_id', $penjualan_id);
            });
        }

        $details = $query->get();
        return response()->json($details);
    }
    public function store(Request $request)
    {
        $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
        ]);

        $margin = ($request->harga_jual - $request->harga_beli) * $request->jumlah;

        $detail = DetailPenjualan::create([
            'penjualan_id' => $request->penjualan_id,
            'barang_id' => $request->barang_id,
            'jumlah' => $request->jumlah,
            'harga_jual' => $request->harga_jual,
            'harga_beli' => $request->harga_beli,
            'margin' => $margin,
        ]);
        $penjualanId = $data['penjualan_id'] ?? $detail->penjualan_id;

        // === QUERY untuk menghitung total harga jual keseluruhan per penjualan_id ===
        $totalHarga = DB::table('detail_penjualan')
            ->where('penjualan_id', $penjualanId)
            ->sum(DB::raw('jumlah * harga_jual'));

        DB::table('penjualan')
            ->where('id', $penjualanId)
            ->update(['total_harga' => $totalHarga]);

        // === Logging lengkap ===
        Log::info('DetailPenjualan berhasil diupdate dan total penjualan diperbarui', [
            'penjualan_id' => $penjualanId,
            'detail_id' => $detail->id,
            'total_harga_keseluruhan' => $totalHarga,
            'data_terbaru' => $detail->fresh()->toArray(),
        ]);
        return response()->json($detail, 201);
    }

    public function show($id)
    {
        $detail = DetailPenjualan::with(['barang', 'penjualan'])->find($id);

        if (!$detail) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($detail);
    }

    public function update(Request $request, $id)
    {
        $detail = DetailPenjualan::findOrFail($id);

        $request->validate([
            'penjualan_id' => 'sometimes|exists:penjualan,id',
            'barang_id' => 'sometimes|exists:barang,id',
            'jumlah' => 'sometimes|integer|min:1',
            'harga_jual' => 'sometimes|numeric|min:0',
            'harga_beli' => 'sometimes|numeric|min:0',
        ]);

        $data = $request->only([
            'penjualan_id',
            'barang_id',
            'jumlah',
            'harga_jual',
            'harga_beli',
        ]);

        if (isset($data['harga_jual'], $data['harga_beli'], $data['jumlah'])) {
            $data['margin'] = ($data['harga_jual'] - $data['harga_beli']) * $data['jumlah'];
        }
        $detail->update($data);

        $penjualanId = $data['penjualan_id'] ?? $detail->penjualan_id;

        // === QUERY untuk menghitung total harga jual keseluruhan per penjualan_id ===
        $totalHarga = DB::table('detail_penjualan')
            ->where('penjualan_id', $penjualanId)
            ->sum(DB::raw('jumlah * harga_jual'));

        DB::table('penjualan')
            ->where('id', $penjualanId)
            ->update(['total_harga' => $totalHarga]);

        // === Logging lengkap ===
        Log::info('DetailPenjualan berhasil diupdate dan total penjualan diperbarui', [
            'penjualan_id' => $penjualanId,
            'detail_id' => $detail->id,
            'total_harga_keseluruhan' => $totalHarga,
            'data_terbaru' => $detail->fresh()->toArray(),
        ]);
        return response()->json($detail);
    }

    public function destroy($id)
    {
        $detail = DetailPenjualan::find($id);

        if (!$detail) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $detail->delete();

        return response()->json(['message' => 'Data berhasil dihapus']);
    }
}
