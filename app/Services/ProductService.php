<?php
// namespace App\Services;
// use App\Repositories\Product\ProductRepository;
// use Exception;
// class ProductService
// {
//     protected $repo;

//     public function __construct(ProductRepository $repo)
//     {
//         $this->repo = $repo;
//     }

//     public function list()
//     {
//         return $this->repo->paginate();
//     }

//     public function detail($id)
//     {
//         return $this->repo->findById($id);
//     }

//     public function store(array $data)
//     {
//         // contoh business rule
//         if ($data['stock'] < 0) {
//             throw new Exception('Stock tidak boleh minus');
//         }

//         return $this->repo->create($data);
//     }

//     public function update($id, array $data)
//     {
//         return $this->repo->update($id, $data);
//     }

//     public function delete($id)
//     {
//         return $this->repo->delete($id);
//     }
// } 



namespace App\Services;

use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $repo;

    public function __construct(ProductRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function paginate($perPage)
    {
        return $this->repo->paginate($perPage);
    }

    public function paginateByUser($userId, $perPage)
    {
        return $this->repo->paginateByUser($userId, $perPage);
    }
    public function findById($id)
    {
        return $this->repo->findById($id);
    }
    public function store(array $data)
    {

        return $this->repo->create($data);
    }
    // public function store($request)
    // {
    //     $data = $request->only([
    //         'shop_id',
    //         'category_id',
    //         'name',
    //         'description',
    //         'price',
    //         'stock',
    //         'imagejson',
    //         'status'
    //     ]);

    //     // ✅ IMAGE UTAMA
    //     if ($request->hasFile('image')) {
    //         $data['image'] = $request->file('image')->store('products', 'public');
    //     }

    //     // ✅ MULTIPLE IMAGE → JSON
    //     $data['imageJson'] = $this->handleMultipleImages($request);

    //     return $this->repo->create($data);
    // }

    public function update($id, $request)
    {
        $product = $this->repo->findById($id);
        $this->deleteImage($id); // Hapus data lama beserta image-nya
        $data = $request->only([
            'shop_id',
            'category_id',
            'name',
            'description',
            'price',
            'stock',
            'status'
        ]);

        // ✅ dari upload file (kalau ada)
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // ✅ dari upload multiple file
        if ($request->hasFile('images')) {
            if (!empty($product->imageJson)) {
                foreach ($product->imageJson as $img) {
                    Storage::disk('public')->delete($img['url']);
                }
            }

            $data['imageJson'] = $this->handleMultipleImages($request);
        }

        // 🔥 TAMBAHAN PENTING (DARI FLUTTER)
        if ($request->filled('image')) {
            $data['image'] = $request->image;
        }

        if ($request->filled('imageJson')) {
            $data['imageJson'] = $request->imageJson;
        }

        \Log::info('FINAL UPDATE DATA', $data);

        return $this->repo->update($id, $data);
    }
    public function deleteImage($id)
    {
        $product = $this->repo->findById($id);

        // ✅ hapus image utama
        if (!empty($product->image)) {
            Storage::disk('public')->delete('uploads/' . $product->image);
        }

        // ✅ hapus multiple images (SUDAH STRING)
        if (!empty($product->imageJson)) {
            foreach ($product->imageJson as $img) {
                if ($img && Storage::disk('public')->exists('uploads/' . $img)) {
                    Storage::disk('public')->delete('uploads/' . $img);
                }
            }
        }

        return true;
    }
    public function delete($id)
    {
        $product = $this->repo->findById($id);

        // ✅ hapus image utama
        if (!empty($product->image)) {
            Storage::disk('public')->delete('uploads/' . $product->image);
        }

        // ✅ hapus multiple images (SUDAH STRING)
        if (!empty($product->imageJson)) {
            foreach ($product->imageJson as $img) {
                if ($img && Storage::disk('public')->exists('uploads/' . $img)) {
                    Storage::disk('public')->delete('uploads/' . $img);
                }
            }
        }

        return $this->repo->delete($id);
    }

    // 🔥 HELPER (biar reusable & clean)
    private function handleMultipleImages($request)
    {
        $images = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('products', 'public');

                $images[] = [
                    'url' => $path,
                    'created_at' => now()->toDateTimeString()
                ];
            }
        }

        return $images;
    }
}
