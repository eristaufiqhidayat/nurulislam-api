<?php

namespace App\Services;

use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class CategoryService
{
    protected $repo;

    public function __construct(CategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAll(int $perPage = 10)
    {
        return $this->repo->paginate($perPage);
    }

    public function listAll()
    {
        return $this->repo->all();
    }

    public function detail(int $id)
    {
        return $this->repo->findById($id);
    }

    public function store(array $data)
    {
        if (!empty($data['parent_id']) && $data['parent_id'] == 0) {
            $data['parent_id'] = null;
        }

        return DB::transaction(function () use ($data) {
            return $this->repo->create($data);
        });
    }

    public function update(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repo->update($id, $data);
        });
    }

    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->repo->delete($id);
        });
    }
}
