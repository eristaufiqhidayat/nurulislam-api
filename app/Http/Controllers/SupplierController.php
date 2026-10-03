<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Tampilkan semua data barang masuk (dengan relasi barang)
     */
    public function index()
    {
        // ambil semua data dengan relasi barang
        $data = Supplier::get();
        return response()->json($data);
    }

    /**
     * Simpan data barang masuk baru
     */
    public function store(Request $request)
    {
        // Log awal
        Log::info('Store Supplier called', [
            'payload' => $request->all()
        ]);

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'telp' => 'required|string',
        ]);

        try {
            $supplier = Supplier::create($request->all());

            // Log jika berhasil
            Log::info('Supplier berhasil dibuat', [
                'supplier' => $supplier
            ]);

            return response()->json([
                'message' => 'Data barang masuk berhasil ditambahkan',
            ], 201);
        } catch (\Exception $e) {

            // Log error jika gagal
            Log::error('Gagal membuat supplier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan detail satu data barang masuk
     */
    public function show($id)
    {
        $data = Supplier::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update data barang masuk
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'telp' => 'required|string',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());
        $supplier->save();

        return response()->json([
            'message' => 'Data barang masuk berhasil diupdate',
        ]);
    }

    /**
     * Hapus data barang masuk
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        // rollback stok
        $supplier = Supplier::find($supplier->id);


        $supplier->delete();

        return response()->json(['message' => 'Data barang masuk berhasil dihapus']);
    }
}
