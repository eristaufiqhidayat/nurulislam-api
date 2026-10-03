<?php
namespace App\Repositories\Cart;

interface CartRepositoryInterface
{
    public function getByUser(int $userId);
    public function add(int $userId, int $productId);
    public function remove(int $id);
    public function clear(int $userId);
    public function updateQuantity(int $id, int $qty);
    public function getUserCartWithItems(int $userId);
    public function clearCart(int $cartId);
}