<?php

namespace App\Http\Controllers;

use App\Models\BarangMasukInv;
use Illuminate\Http\Request;

class BarangMasukInvController extends Controller
{
    public function index()
    {
        \DB::enableQueryLog();

        $data = BarangMasukInv::with('supplier', 'barangMasuk.barang')
            ->latest()
            ->get();

        $query = \DB::getQueryLog();

        \Log::info('SQL Query:', $query);

        return response()->json([
            'data' => $data,
            'sql'  => $query
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_supplier' => 'required|integer|exists:supplier,id',
            'tanggal'     => 'required|date',
        ]);

        $data = BarangMasukInv::create($request->all());

        return response()->json([
            'message' => 'Data Barang Masuk berhasil ditambahkan',
            'data' => $data
        ], 201);
    }

    public function show($id)
    {
        $data = BarangMasukInv::with('supplier', 'barangMasuk.barang')->find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
$query = \DB::getQueryLog();
        return response()->json($data);
        // return response()->json([
        //     'data' => $data,
        //      'sql'  => $query
        // ]);
    }

    public function update(Request $request, $id)
    {
        $data = BarangMasukInv::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'id_supplier' => 'required|integer|exists:suppliers,id',
            'tanggal'     => 'required|date',
        ]);

        $data->update($request->all());

        return response()->json([
            'message' => 'Data Barang Masuk berhasil diperbarui',
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $data = BarangMasukInv::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();

        return response()->json(['message' => 'Data Barang Masuk berhasil dihapus']);
    }
}
