<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected ShopService $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }

    /** list by login user */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => $this->service->paginateByUser(
                $userId,
                $request->get('per_page', 10)
            )
        ]);
    }
    public function list()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->listAll()
        ]);
    }
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id)
        ]);
    }
    /** create & update */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id'          => 'nullable|integer',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'logo'        => 'nullable|string',
        ]);

        $data['user_id'] = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => $this->service->save($data, $request->id),
        ]);
    }
    public function update(Request $request, int $id)
    {
        try {
            // ✅ Validasi (sesuaikan dengan field tabel kamu)
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'logo' => 'nullable|string', // kalau base64 / url
            ]);

            // ✅ Update via service (pakai save)
            $shop = $this->service->save($validated, $id);

            return response()->json([
                'success' => true,
                'message' => 'Data shop berhasil diupdate',
                'data' => $shop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update shop',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Shop berhasil dihapus'
        ]);
    }
}
