<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Tampilkan semua barang.
     */
    public function index(Request $request)
    {
        $query = Barang::orderBy('id', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama_barang', 'like', "%$search%")
                ->orWhere('kategori', 'like', "%$search%");
        }

        $barang = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'List Barang',
            'data' => $barang->items(),
            'meta' => [
                'current_page' => $barang->currentPage(),
                'last_page' => $barang->lastPage(),
                'per_page' => $barang->perPage(),
                'total' => $barang->total(),
            ],
        ]);
    }


    /**
     * Simpan data barang baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $barang = Barang::create($validated);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    /**
     * Tampilkan satu data barang berdasarkan id.
     */
    public function show($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json($barang);
    }

    /**
     * Update data barang.
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'satuan' => 'nullable|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $barang->update($validated);

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => $barang
        ]);
    }

    /**
     * Hapus data barang.
     */
    public function destroy($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
