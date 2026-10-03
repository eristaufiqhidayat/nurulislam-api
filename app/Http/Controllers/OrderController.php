<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        $order = $this->service->createOrder($userId, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order
        ]);
    }

    public function myOrders(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'success' => true,
            'data' => $this->service->getUserOrders($userId)
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->getDetail($id)
        ]);
    }
}