<?php

namespace App\Http\Controllers;

use App\Models\BarangHarga;
use Illuminate\Http\Request;

class BarangHargaController extends Controller
{
    /**
     * Tampilkan semua harga barang
     */
    public function index()
    {
        $data = BarangHarga::with('barang')->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Simpan data harga baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'tanggal'    => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $harga = BarangHarga::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Harga berhasil ditambahkan',
            'data' => $harga
        ], 201);
    }

    /**
     * Tampilkan detail harga berdasarkan ID
     */
    public function show($id)
    {
        $harga = BarangHarga::with('barang')->find($id);

        if (!$harga) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $harga
        ]);
    }

    /**
     * Update data harga
     */
    public function update(Request $request, $id)
    {
        $harga = BarangHarga::find($id);

        if (!$harga) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'barang_id'  => 'required|exists:barang,id',
            'tanggal'    => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $harga->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui',
            'data' => $harga
        ]);
    }

    /**
     * Hapus data harga
     */
    public function destroy($id)
    {
        $harga = BarangHarga::find($id);

        if (!$harga) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $harga->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /**
     * Ambil harga berdasarkan barang_id dan tanggal tertentu
     */
    public function getHarga(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|integer|exists:barang,id',
            'tanggal'   => 'required|date',
        ]);

        $barangId = $request->barang_id;
        $tanggal  = $request->tanggal;

        // Ambil harga yang berlaku pada tanggal tersebut
        // yaitu harga terakhir yang tanggal <= tanggal barang masuk
        $harga = BarangHarga::where('barang_id', $barangId)
            ->whereDate('tanggal', '<=', $tanggal)
            ->orderBy('tanggal', 'desc')
            ->first();

        if (!$harga) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada harga yang berlaku untuk tanggal tersebut',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'barang_id' => $barangId,
            'tanggal_berlaku' => $harga->tanggal,
            'tanggal_input' => $tanggal,
            'harga_beli' => $harga->harga_beli,
            'harga_jual' => $harga->harga_jual,
        ]);
    }
}
