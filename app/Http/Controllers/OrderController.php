<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Store a newly created order with distinct item types (Product, Sauce, Combo).
     */
    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        try {
            $order = $orderService->createOrder($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'orderCode' => $order->order_code,
                'totalAmount' => $order->total_amount,
                'order' => $order,
            ], 201);
        } catch (Exception $e) {
            Log::error('Order creation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi tạo đơn hàng: '.$e->getMessage(),
            ], 500);
        }
    }
}
