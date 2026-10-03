<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Cart\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected $orderRepo;
    protected $cartRepo;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        CartRepositoryInterface $cartRepo
    ) {
        $this->orderRepo = $orderRepo;
        $this->cartRepo = $cartRepo;
    }

    public function checkout(int $userId, array $data)
    {
        DB::beginTransaction();

        try {
            $cart = $this->cartRepo->getUserCartWithItems($userId);

            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception('Cart kosong');
            }

            $total = 0;

            foreach ($cart->items as $item) {
                $total += $item->quantity * $item->product->price;
            }

            $order = $this->orderRepo->create([
                'user_id' => $userId,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'payment_status' => 'unpaid'
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'subtotal' => $item->quantity * $item->product->price
                ]);
            }

            $address = $data['address'] ?? [];

            OrderAddress::create([
                'order_id' => $order->id,
                'receiver_name' => $address['name'] ?? 'Guest',
                'phone' => $address['phone'] ?? '-',
                'address' => $address['address'] ?? '-',
                'city' => $address['city'] ?? '-',
                'postal_code' => $address['postal_code'] ?? '-'
            ]);

            $this->cartRepo->clearCart($cart->id);

            DB::commit();

            return $order->load(['items', 'address']);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
