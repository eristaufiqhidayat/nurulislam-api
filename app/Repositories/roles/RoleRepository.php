<?php

namespace App\Repositories\roles;

use App\Models\Role;
use App\Repositories\roles\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    public function all()
    {
        return Role::orderBy('id', 'desc')->get();
    }

    public function find($id): ?Role
    {
        return Role::find($id);
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update($id, array $data): bool
    {
        $role = Role::findOrFail($id);
        return $role->update($data);
    }

    public function delete($id): bool
    {
        return Role::destroy($id) > 0;
    }
}
