<?php

namespace App\Services;

use App\Repositories\Menu\MenuRepositoryInterface;

class MenuService
{
    protected $menuRepo;

    public function __construct(MenuRepositoryInterface $menuRepo)
    {
        $this->menuRepo = $menuRepo;
    }

    public function list()
    {
        return $this->menuRepo->getAll();
    }

    public function store(array $data)
    {
        return $this->menuRepo->create($data);
    }

    public function show(int $id)
    {
        return $this->menuRepo->findById($id);
    }

    public function update(int $id, array $data)
    {
        return $this->menuRepo->update($id, $data);
    }

    public function destroy(int $id)
    {
        return $this->menuRepo->delete($id);
    }
}
