<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Coupon;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xác thực và tính toán số tiền giảm giá của mã Coupon.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $code = strtoupper(trim((string) $request->input('code', '')));
        $subtotal = (float) $request->input('subtotal', 0);

        if ($code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá.',
            ], 422);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá "' . $code . '" không tồn tại.',
            ], 404);
        }

        $validation = $coupon->isValidFor($subtotal);
        if (! $validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message'],
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return response()->json([
            'success' => true,
            'coupon_code' => $coupon->code,
            'coupon_name' => $coupon->name,
            'discount_amount' => $discount,
            'formatted_discount' => '-' . number_format($discount, 0, ',', '.') . 'đ',
            'message' => 'Áp dụng mã "' . $coupon->code . '" thành công! Bạn được giảm ' . number_format($discount, 0, ',', '.') . 'đ.',
        ]);
    }
}
