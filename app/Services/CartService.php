<?php

namespace App\Services;

use App\Repositories\Cart\CartRepositoryInterface;

class CartService
{
    protected CartRepositoryInterface $repo;

    public function __construct(CartRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getCart(int $userId)
    {
        return $this->repo->getByUser($userId);
    }

    public function addToCart(int $userId, int $productId)
    {
        return $this->repo->add($userId, $productId);
    }

    public function removeCart(int $id)
    {
        return $this->repo->remove($id);
    }

    public function clearCart(int $userId)
    {
        return $this->repo->clear($userId);
    }
    public function updateQty(int $id, int $qty)
    {
        return $this->repo->updateQuantity($id, $qty);
    }
}
