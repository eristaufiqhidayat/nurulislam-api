<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CartRepository implements CartRepositoryInterface
{
    public function getByUser(int $userId)
    {
        return Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();
    }

    public function add(int $userId, int $productId)
    {
        // 1. cari / buat cart
        $cart = Cart::firstOrCreate([
            'user_id' => $userId
        ]);

        // 2. cek item
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->increment('quantity');
            return $item;
        }

        // 3. ambil harga dari product
        $product = \App\Models\Product::findOrFail($productId);

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'quantity' => 1,
            'price' => $product->price,
        ]);
    }
    public function updateQuantity(int $id, int $qty)

    {
        DB::listen(function ($query) {
            Log::info('SQL Query', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
            ]);
        });
        $item = CartItem::findOrFail($id);

        $item->update([
            'quantity' => $qty
        ]);
        return $item;
    }
    public function remove(int $id)
    {
        return CartItem::findOrFail($id)->delete();
    }

    public function clear(int $userId)
    {
        return Cart::where('user_id', $userId)->delete();
    }
    public function getUserCartWithItems(int $userId)
    {
        return Cart::with('items.product')
            ->where('user_id', $userId)
            ->first();
    }

    public function clearCart(int $cartId)
    {
        $cart = Cart::findOrFail($cartId);
        $cart->items()->delete();
    }
}
