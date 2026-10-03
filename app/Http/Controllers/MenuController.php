<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Http\Controllers\Controller;
use App\Services\MenuService;

class MenuController extends Controller
{
    // app/Http/Controllers/MenuController.php
    protected $service;

    public function __construct(MenuService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        // $perPage = $request->get('per_page', 10);
        // $menus = Menu::orderBy('order')->paginate($perPage);
        // return response()->json($menus);
        return response()->json($this->service->list());
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'order'     => 'required|integer',
            'color'     => 'nullable|string|max:50',
        ]);

        return response()->json([
            'message' => 'Menu berhasil ditambahkan',
            'data' => $this->service->store($data)
        ], 201);
    }

    public function show($id)
    {
        return response()->json($this->service->show($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'route'     => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'order'     => 'required|integer',
            'color'     => 'nullable|string|max:50',
        ]);

        return response()->json([
            'message' => 'Menu berhasil diupdate',
            'data' => $this->service->update($id, $data)
        ]);
    }

    public function destroy($id)
    {
        $this->service->destroy($id);

        return response()->json([
            'message' => 'Menu berhasil dihapus'
        ]);
    }
}
