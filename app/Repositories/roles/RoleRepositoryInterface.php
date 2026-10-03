<?php

namespace App\Repositories\roles;

use App\Models\Role;

interface RoleRepositoryInterface
{
    public function all();
    public function find($id): ?Role;
    public function create(array $data): Role;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}
