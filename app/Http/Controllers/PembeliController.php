<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PembeliController extends Controller
{
    public function index()
    {
        $data = Pembeli::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'List Data Pembeli',
            'data'    => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'        => 'required|string|max:255',
            'alamat'      => 'required|string',
            'no_telepon'  => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pembeli = Pembeli::create($request->only(['nama', 'alamat', 'no_telepon']));

        return response()->json([
            'success' => true,
            'message' => 'Data Pembeli berhasil disimpan',
            'data'    => $pembeli
        ]);
    }

    public function show($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pembeli tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $pembeli
        ]);
    }

    public function update(Request $request, $id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pembeli tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama'        => 'required|string|max:255',
            'alamat'      => 'required|string',
            'no_telepon'  => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pembeli->update($request->only(['nama', 'alamat', 'no_telepon']));

        return response()->json([
            'success' => true,
            'message' => 'Data Pembeli berhasil diperbarui',
            'data'    => $pembeli
        ]);
    }

    public function destroy($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json([
                'success' => false,
                'message' => 'Data Pembeli tidak ditemukan'
            ], 404);
        }

        $pembeli->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Pembeli berhasil dihapus'
        ]);
    }
}
