<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getAll(10)
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
            'data' => $this->service->detail($id)
        ]);
    }

    public function store(CategoryStoreRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->store($request->validated())
        ], 201);
    }

    public function update(CategoryUpdateRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->update($id, $request->validated())
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Category berhasil dihapus'
        ]);
    }
}
