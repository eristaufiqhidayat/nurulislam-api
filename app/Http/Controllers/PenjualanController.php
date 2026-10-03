<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = Penjualan::with('details', 'Pembeli', 'details.barang')->orderBy('id', 'desc')->get();
        return response()->json($penjualan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pembeli' => 'required|string|max:100',
            'details' => 'required|array|min:1',
            'details.*.barang_nama' => 'required|string|max:100',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $penjualan = Penjualan::create([
                'tanggal' => $request->tanggal,
                'pembeli' => $request->pembeli,
                'total' => 0,
            ]);

            $total = 0;

            foreach ($request->details as $detail) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_nama' => $detail['barang_nama'],
                    'jumlah' => $detail['jumlah'],
                    'harga' => $detail['harga'],
                ]);

                $total += $detail['jumlah'] * $detail['harga'];
            }

            $penjualan->update(['total' => $total]);

            DB::commit();
            return response()->json(['message' => 'Penjualan berhasil disimpan', 'data' => $penjualan->load('details')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        DB::listen(function ($query) {
            Log::info("SQL QUERY", [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time
            ]);
        });
        $penjualan = Penjualan::with(['pembeli', 'details'])->find($id);

        if (!$penjualan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($penjualan);
    }

    public function update(Request $request, $id)
    {
        $penjualan = Penjualan::find($id);

        if (!$penjualan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'pembeli' => 'required|string|max:100',
            'details' => 'required|array|min:1',
            'details.*.barang_nama' => 'required|string|max:100',
            'details.*.jumlah' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $penjualan->update([
                'tanggal' => $request->tanggal,
                'pembeli' => $request->pembeli,
            ]);

            // Hapus semua detail lama lalu insert ulang
            DetailPenjualan::where('penjualan_id', $penjualan->id)->delete();

            $total = 0;

            foreach ($request->details as $detail) {
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_nama' => $detail['barang_nama'],
                    'jumlah' => $detail['jumlah'],
                    'harga' => $detail['harga'],
                ]);

                $total += $detail['jumlah'] * $detail['harga'];
            }

            $penjualan->update(['total' => $total]);

            DB::commit();
            return response()->json(['message' => 'Penjualan berhasil diperbarui', 'data' => $penjualan->load('details')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        if (!$penjualan) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        // 🔹 Hapus semua detail penjualan terkait
        $penjualan->details()->delete();

        // 🔹 Hapus penjualan utama
        $penjualan->delete();

        return response()->json(['message' => 'Penjualan berhasil dihapus']);
    }
}
