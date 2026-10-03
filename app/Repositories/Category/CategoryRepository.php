<?php

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Category::with('children')
            ->whereNull('parent_id')
            ->latest()
            ->paginate($perPage);
    }

    public function all()
    {
        return Category::with('children')->get();
    }

    public function findById(int $id): Category
    {
        return Category::with('children')->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->findById($id);
        $category->update($data);

        return $category;
    }

    public function delete(int $id): bool
    {
        return Category::destroy($id);
    }
}
