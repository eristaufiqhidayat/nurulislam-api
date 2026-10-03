<?php
namespace App\Repositories\Menu;

use App\Models\Menu;

class MenuRepository implements MenuRepositoryInterface
{
    public function getAll()
    {
        return Menu::orderBy('parent_id')
            ->orderBy('order')
            ->get();
    }

    public function findById(int $id): ?Menu
    {
        return Menu::find($id);
    }

    public function create(array $data): Menu
    {
        return Menu::create($data);
    }

    public function update(int $id, array $data): Menu
    {
        $menu = Menu::findOrFail($id);
        $menu->update($data);
        return $menu;
    }

    public function delete(int $id): bool
    {
        return Menu::destroy($id);
    }
}
