<?php


namespace App\Http\Controllers;

use App\Models\PageInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PageInfoController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Request data: ', $request->all());
        \Log::info('Category query: ' . $request->get('category'));
        $query = PageInfo::query();
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->has('per_page')) {
            $perPage = $request->get('per_page', 10);
            return response()->json($query->paginate($perPage));
        } else {
            return response()->json($query->get());
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'category' => 'nullable|string',
        ]);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $pageInfo = PageInfo::create($data);

        return response()->json($pageInfo, 201);
    }

    public function show($id)
    {
        $pageInfo = PageInfo::findOrFail($id);
        return response()->json($pageInfo);
    }

    public function update(Request $request, $id)
    {
        $pageInfo = PageInfo::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $pageInfo->update($data);

        return response()->json($pageInfo);
    }

    public function destroy($id)
    {
        $pageInfo = PageInfo::findOrFail($id);
        $pageInfo->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
    public function upload(Request $request)
    {
        Log::info("=== MULAI UPLOAD GAMBAR ===");

        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            Log::info("Validasi OK");

            $file = $request->file('image');

            Log::info("File diterima", [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');

            Log::info("File berhasil disimpan", [
                'filename' => $filename,
                'path' => $path,
            ]);

            $url = asset('storage/' . $path);

            Log::info("Upload BERHASIL", ['url' => $url]);

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'filename' => $filename,
                'url' => $url,
            ]);
        } catch (\Exception $e) {

            Log::error("Upload GAGAL", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
