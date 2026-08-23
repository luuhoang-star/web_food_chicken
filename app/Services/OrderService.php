<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sauce;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create an order and its associated items within a database transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            // Free shipping threshold for orders >= 100,000 VND
            $shippingFee = ($subtotal >= 100000) ? 0 : 15000;
            $totalAmount = $subtotal + $shippingFee;

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
                'discount' => 0,
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
    }
}
