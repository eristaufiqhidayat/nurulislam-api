<?php

namespace App\Http\Controllers;

use App\Models\PageInfo;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    private function category(string $category): string
    {
        abort_unless(in_array($category, ['kegiatan', 'kajian'], true), 404);
        return $category;
    }

    public function index(Request $request, string $category)
    {
        $category = $this->category($category);
        $query = PageInfo::where('category', $category)->latest();

        if ($request->has('per_page')) {
            $perPage = max(1, min((int) $request->get('per_page', 10), 100));
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    public function show(string $category, $id)
    {
        $category = $this->category($category);
        return response()->json(
            PageInfo::where('category', $category)->findOrFail($id)
        );
    }

    public function store(Request $request, string $category)
    {
        $category = $this->category($category);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);
        $data['category'] = $category;

        return response()->json(PageInfo::create($data), 201);
    }

    public function update(Request $request, string $category, $id)
    {
        $category = $this->category($category);
        $item = PageInfo::where('category', $category)->findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);
        $data['category'] = $category;
        $item->update($data);

        return response()->json($item->fresh());
    }

    public function destroy(string $category, $id)
    {
        $category = $this->category($category);
        $item = PageInfo::where('category', $category)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => ucfirst($category) . ' deleted successfully']);
    }
}
