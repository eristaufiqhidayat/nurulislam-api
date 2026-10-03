<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Tampilkan semua data barang masuk (dengan relasi barang)
     */
    public function index()
    {
        // ambil semua data dengan relasi barang
        $data = BarangMasuk::with('barang')->orderBy('id', 'desc')->get();
        return response()->json($data);
    }

    /**
     * Simpan data barang masuk baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_inv'=> 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        $barangMasuk = BarangMasuk::create($request->all());

        // update stok barang
        $barang = Barang::find($request->barang_id);
        $barang->stok += $request->jumlah;
        $barang->save();

        return response()->json([
            'message' => 'Data barang masuk berhasil ditambahkan',
            'data' => $barangMasuk->load('barang')
        ], 201);
    }

    /**
     * Tampilkan detail satu data barang masuk
     */
    public function show($id)
    {
        $data = BarangMasuk::with('barang')->findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data barang masuk
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'harga_beli' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
        ]);

        $barangMasuk = BarangMasuk::findOrFail($id);
        $barangMasuk->update($request->all());
        $totalStok = BarangMasuk::where('barang_id', $request->barang_id)->sum('jumlah');           
        // update stok barang (hitung selisih)
        $barang = Barang::find($request->barang_id);
        $barang->stok = $totalStok;
        $barang->save();

        return response()->json([
            'message' => 'Data barang masuk berhasil diupdate',
            'data' => $barangMasuk->load('barang')
        ]);
    }

    /**
     * Hapus data barang masuk
     */
    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);

        // rollback stok
        $barang = Barang::find($barangMasuk->barang_id);
        if ($barang) {
            $barang->stok -= $barangMasuk->jumlah;
            $barang->save();
        }

        $barangMasuk->delete();

        return response()->json(['message' => 'Data barang masuk berhasil dihapus']);
    }
}
