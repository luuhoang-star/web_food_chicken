<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected ?TelegramService $telegramService = null
    ) {
        $this->telegramService ??= app(TelegramService::class);
    }

    /**
     * Create an order and its associated items within a database transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            // Đọc cấu hình phí ship động từ SiteSetting
            $defaultShip = (float) (SiteSetting::get('shipping_fee_default') ?: 15000);
            $freeshipThreshold = (float) (SiteSetting::get('freeship_threshold') ?: 100000);
            $shippingFee = ($subtotal >= $freeshipThreshold) ? 0 : $defaultShip;

            // Xử lý mã giảm giá Coupon nếu có
            $discount = 0;
            if (! empty($data['couponCode'])) {
                $coupon = Coupon::where('code', strtoupper(trim((string) $data['couponCode'])))->first();
                if ($coupon) {
                    $validation = $coupon->isValidFor($subtotal);
                    if ($validation['valid']) {
                        $discount = $coupon->calculateDiscount($subtotal);
                        $coupon->increment('used_count');
                    }
                }
            } elseif (! empty($data['discount']) && is_numeric($data['discount'])) {
                $discount = (float) $data['discount'];
            }

            $totalAmount = max(0, $subtotal + $shippingFee - $discount);

            // Unique Order Code: GAO-XXXXXX
            $orderCode = 'GAO-'.strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'order_code' => $orderCode,
                'customer_name' => $data['fullName'],
                'customer_phone' => $data['phone'],
                'district' => $data['district'],
                'address' => $data['address'],
                'driver_note' => $data['driverNote'] ?? null,
                'payment_method' => $data['paymentMethod'],
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total_amount' => $totalAmount,
            ]);

            foreach ($data['items'] as $item) {
                $itemType = $item['item_type'] ?? 'product';
                $productId = $item['product_id'] ?? null;
                $sauceId = $item['sauce_id'] ?? null;

                // Auto match sauce ID if item_type is sauce
                if ($itemType === 'sauce' && ! $sauceId) {
                    $matchedSauce = Sauce::where('name', $item['name'])->first();
                    $sauceId = $matchedSauce?->id;
                }

                // Auto match product ID if item_type is product
                if ($itemType === 'product' && ! $productId) {
                    $matchedProduct = Product::where('name', $item['name'])->first();
                    $productId = $matchedProduct?->id;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $itemType,
                    'product_id' => $productId,
                    'sauce_id' => $sauceId,
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

            return $order->load('items');
        });

        // Trigger asynchronous/non-blocking Telegram notification
        try {
            $this->telegramService->sendOrderNotification($order);
        } catch (\Throwable $e) {
            Log::error('Telegram notification error: '.$e->getMessage());
        }

        return $order;
    }
}
