<?php

namespace App\Repositories\Shop;

use App\Models\Shop;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ShopRepositoryInterface
{
    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function all();
    public function find(int $id): ?Shop;
    public function store(array $data): Shop;
    public function update(int $id, array $data): Shop;
    public function delete(int $id): bool;
}
