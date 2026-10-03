<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected CartService $service;

    public function __construct(CartService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => $this->service->getCart($userId)
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = $this->service->addToCart(
            $userId,
            (int) $request->product_id
        );

        return response()->json([
            'success' => true,
            'data' => $cart
        ]);
    }

    public function destroy($id)
    {
        $this->service->removeCart((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Item removed'
        ]);
    }

    public function clear(Request $request)
    {
        $userId = $request->user()->id;

        $this->service->clearCart($userId);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }
    public function update(Request $request, int $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = $this->service->updateQty($id, $request->quantity);

        return response()->json([
            'success' => true,
            'data' => $item
        ]);
    }
}
