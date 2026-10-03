<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Product::with(['shop', 'category'])
            ->latest()
            ->paginate($perPage);
    }
    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Product::with(['shop', 'category'])
            ->whereHas('shop', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->latest()
            ->paginate($perPage);
    }
    public function findById(int $id): Product
    {
        return Product::with(['shop', 'category'])
            ->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->findById($id);
        $product->update($data);

        return $product;
    }

    public function delete(int $id): bool
    {
        return Product::destroy($id);
    }
}
