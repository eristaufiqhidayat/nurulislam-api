<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $service;

    public function __construct(CheckoutService $service)
    {
        $this->service = $service;
    }

    public function checkout(Request $request)
    {
        $userId = $request->user()->id;

        $order = $this->service->checkout($userId, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil',
            'data' => $order
        ]);
    }
}
