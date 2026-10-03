<?php

namespace App\Repositories\Shop;

use App\Models\Shop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShopRepository implements ShopRepositoryInterface
{
    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Shop::where('user_id', $userId)
            ->orderByDesc('id')
            ->paginate($perPage);
    }
    public function all()
    {
        return Shop::query()
            ->orderBy('id', 'desc')
            ->get();
    }

    public function find(int $id): ?Shop
    {
        return Shop::find($id);
    }

    public function store(array $data): Shop
    {
        return Shop::create($data);
    }

    public function update(int $id, array $data): Shop
    {
        $shop = Shop::findOrFail($id);
        $shop->update($data);
        return $shop;
    }

    public function delete(int $id): bool
    {
        return (bool) Shop::where('id', $id)->delete();
    }
}
