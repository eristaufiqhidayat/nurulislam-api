<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'data' => $this->service->getAll(),
            'message' => 'success'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $role = $this->service->store($request->only('name'));

        return response()->json([
            'message' => 'Role berhasil ditambahkan',
            'data' => $role
        ], 201);
    }

    public function show($id)
    {
        $role = $this->service->getById($id);

        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], 404);
        }

        return response()->json(['data' => $role]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $this->service->update($id, $request->only('name'));

        return response()->json([
            'message' => 'Role berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $this->service->destroy($id);

        return response()->json([
            'message' => 'Role berhasil dihapus'
        ]);
    }
}
