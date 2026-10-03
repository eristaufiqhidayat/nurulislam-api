<?php

namespace App\Services;

use App\Models\Shop;
use App\Repositories\Shop\ShopRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShopService
{
    protected ShopRepositoryInterface $repository;

    public function __construct(ShopRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function paginateByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginateByUser($userId, $perPage);
    }

    public function find(int $id): ?Shop
    {
        return $this->repository->find($id);
    }
    public function listAll()
    {
        return $this->repository->all();
    }
    /** store & update jadi satu */
    public function save(array $data, ?int $id = null): Shop
    {
        return $id
            ? $this->repository->update($id, $data)
            : $this->repository->store($data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
