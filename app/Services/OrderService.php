<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderRepositoryInterface $repo;

    public function __construct(OrderRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function createOrder(int $userId, array $data)
    {
        DB::beginTransaction();

        try {
            $order = $this->repo->create([
                'user_id' => $userId,
                'total_amount' => $data['total_amount'],
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid'
            ]);

            // items
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price']
                ]);
            }

            // address
            OrderAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $data['address']['name'],
                'phone' => $data['address']['phone'],
                'address' => $data['address']['address'],
                'city' => $data['address']['city'],
                'postal_code' => $data['address']['postal_code']
            ]);

            DB::commit();

            return $this->repo->findById($order->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserOrders(int $userId)
    {
        return $this->repo->findByUser($userId);
    }

    public function getDetail(int $id)
    {
        return $this->repo->findById($id);
    }
}