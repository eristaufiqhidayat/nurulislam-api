<?php

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findByUser(int $userId): Collection
    {
        return Order::with(['items', 'address','items.product'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function findById(int $id): Order
    {
        return Order::with(['items', 'address'])
            ->findOrFail($id);
    }
    public function createOrder(array $data)
    {
        return Order::create($data);
    }

    public function createOrderItems(int $orderId, array $items)
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ]);
        }
    }
}
