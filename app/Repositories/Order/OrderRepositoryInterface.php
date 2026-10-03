<?php

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function create(array $data): Order;

    public function findByUser(int $userId): Collection;

    public function findById(int $id): Order;
    public function createOrder(array $data);
    public function createOrderItems(int $orderId, array $items);
}