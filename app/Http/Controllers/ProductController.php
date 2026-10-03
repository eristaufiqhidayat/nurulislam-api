<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use Exception;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->paginate(100)
        ]);
    }
    public function paginateByUser(Request $request)
    {
        $userId = $request->user()->id;
        return response()->json([
            'success' => true,
            'data' => $this->service->paginateByUser($userId, 100)
        ]);
    }
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->findById($id)
        ]);
    }

    public function store(ProductStoreRequest $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->store($request->validated())
        ], 201);
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->update($id, $request)
        ]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Product berhasil dihapus'
        ]);
    }
    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $files = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $name = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('storage/uploads'), $name);
                $files[] = $name;
            }
        }

        return response()->json([
            'files' => $files
        ]);
    }
}
