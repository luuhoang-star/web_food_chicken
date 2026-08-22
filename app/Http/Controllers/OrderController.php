<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Store a newly created order in database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'district' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'driverNote' => 'nullable|string|max:500',
            'paymentMethod' => 'required|string|in:cod,momo,vnpay,zalopay',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.sauce' => 'nullable|string',
            'items.*.spiceLevel' => 'nullable|string',
            'items.*.toppings' => 'nullable|array',
            'items.*.note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            // Free ship for orders >= 100,000 VND
            $shippingFee = ($subtotal >= 100000) ? 0 : 15000;
            $totalAmount = $subtotal + $shippingFee;

            // Generate unique order code: GAO-XXXXXX
            $orderCode = 'GAO-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_name' => $validated['fullName'],
                'customer_phone' => $validated['phone'],
                'district' => $validated['district'],
                'address' => $validated['address'],
                'driver_note' => $validated['driverNote'] ?? null,
                'payment_method' => $validated['paymentMethod'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => 0,
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => null, // Optional matching
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'sauce' => $item['sauce'] ?? null,
                    'spice_level' => $item['spiceLevel'] ?? null,
                    'toppings' => $item['toppings'] ?? [],
                    'note' => $item['note'] ?? null,
                    'total_item_price' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'orderCode' => $order->order_code,
                'totalAmount' => $order->total_amount,
                'order' => $order->load('items'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage(),
            ], 500);
        }
    }
}
