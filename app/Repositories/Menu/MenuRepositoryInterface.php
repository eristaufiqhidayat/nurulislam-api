<?php
namespace App\Repositories\Menu;

use App\Models\Menu;

interface MenuRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?Menu;
    public function create(array $data): Menu;
    public function update(int $id, array $data): Menu;
    public function delete(int $id): bool;
}
