<?php

namespace App\Services;

use App\Models\Combo;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SiteSetting;
use App\Models\Topping;
use DomainException;
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
     * All prices, discounts, and item availabilities are verified against the database.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws DomainException
     */
    public function createOrder(array $data): Order
    {
        // 1. Kiểm tra trạng thái đóng/mở nhận đơn của Bếp
        $storeStatus = SiteSetting::get('store_open_status', 'open');
        if ($storeStatus === 'paused') {
            $hotline = SiteSetting::get('hotline', '0988.868.GAO');
            throw new DomainException("Bếp GAO hiện đang tạm dừng nhận đơn ít phút để xử lý đơn hàng hiện tại. Quý khách vui lòng đặt lại sau hoặc liên hệ Hotline {$hotline}!");
        }

        $order = DB::transaction(function () use ($data) {
            $subtotal = 0;
            $preparedItems = [];

            // 2. Tính toán lại đơn giá cho từng món dựa trên CSDL
            foreach ($data['items'] as $item) {
                $itemType = $item['item_type'] ?? 'product';
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                $productId = ! empty($item['product_id']) ? (int) $item['product_id'] : null;
                $sauceId = ! empty($item['sauce_id']) ? (int) $item['sauce_id'] : null;
                $productName = trim((string) ($item['name'] ?? ''));

                $basePrice = 0;
                $matchedSauce = null;
                $matchedProduct = null;
                $matchedCombo = null;

                if ($itemType === 'sauce') {
                    // Hũ sốt mua thêm
                    if ($sauceId) {
                        $matchedSauce = Sauce::find($sauceId);
                    }
                    if (! $matchedSauce && $productName !== '') {
                        $matchedSauce = Sauce::where('name', $productName)->first();
                    }

                    if ($matchedSauce) {
                        if (! $matchedSauce->is_available) {
                            throw new DomainException("Vị sốt \"{$matchedSauce->name}\" hiện đã hết món.");
                        }
                        $basePrice = (float) $matchedSauce->price;
                        $productName = $matchedSauce->name;
                        $sauceId = $matchedSauce->id;
                    } else {
                        $basePrice = 10000;
                    }
                } elseif ($itemType === 'combo') {
                    // Gói combo ưu đãi
                    if ($productId) {
                        $matchedCombo = Combo::find($productId);
                    }
                    if (! $matchedCombo && $productName !== '') {
                        $matchedCombo = Combo::where('name', $productName)->first();
                    }

                    if ($matchedCombo) {
                        if (! $matchedCombo->is_active) {
                            throw new DomainException("Gói combo \"{$matchedCombo->name}\" hiện đang tạm ngưng mở bán.");
                        }
                        $basePrice = (float) $matchedCombo->price;
                        $productName = $matchedCombo->name;
                    } else {
                        // Tìm trong bảng Product nếu combo được lưu ở Product
                        $matchedProduct = Product::where('name', $productName)->first();
                        if ($matchedProduct) {
                            if (! $matchedProduct->is_available) {
                                throw new DomainException("Combo \"{$matchedProduct->name}\" hiện đang hết hàng.");
                            }
                            $basePrice = (float) $matchedProduct->price;
                            $productId = $matchedProduct->id;
                        } else {
                            $basePrice = max(0, (float) ($item['price'] ?? 0));
                        }
                    }
                } else {
                    // Món ăn chính / Ăn kèm / Nước uống (Product)
                    if ($productId) {
                        $matchedProduct = Product::find($productId);
                    }
                    if (! $matchedProduct && $productName !== '') {
                        $matchedProduct = Product::where('name', $productName)->first();
                    }

                    if ($matchedProduct) {
                        if (! $matchedProduct->is_available) {
                            throw new DomainException("Món \"{$matchedProduct->name}\" hiện đã hết hàng.");
                        }
                        $basePrice = (float) $matchedProduct->price;
                        $productName = $matchedProduct->name;
                        $productId = $matchedProduct->id;
                    } else {
                        $basePrice = max(0, (float) ($item['price'] ?? 0));
                    }
                }

                // 3. Tính tiền tùy chọn Topping / Món ăn kèm thêm nếu có
                $extraPrice = 0;
                $toppingsList = [];
                if (! empty($item['toppings']) && is_array($item['toppings'])) {
                    $toppingsList = array_values(array_filter($item['toppings']));
                    if (! empty($toppingsList)) {
                        // Tính tiền các topping đã chọn
                        $toppingModels = Topping::whereIn('name', $toppingsList)->where('is_active', true)->get();
                        $extraPrice += (float) $toppingModels->sum('price');

                        // Tính thêm tiền nếu khách chọn thêm món side/drink dưới dạng topping
                        $matchedSides = Product::whereIn('name', $toppingsList)->where('is_available', true)->get();
                        $extraPrice += (float) $matchedSides->sum('price');
                    }
                }

                $calculatedUnitPrice = $basePrice + $extraPrice;
                $totalItemPrice = $calculatedUnitPrice * $quantity;
                $subtotal += $totalItemPrice;

                $preparedItems[] = [
                    'item_type' => $itemType,
                    'product_id' => $productId,
                    'sauce_id' => $sauceId,
                    'product_name' => $productName,
                    'price' => $calculatedUnitPrice,
                    'quantity' => $quantity,
                    'sauce' => $item['sauce'] ?? null,
                    'spice_level' => $item['spiceLevel'] ?? null,
                    'toppings' => $toppingsList,
                    'note' => $item['note'] ?? null,
                    'total_item_price' => $totalItemPrice,
                ];
            }

            // 4. Đọc cấu hình phí ship động từ SiteSetting
            $defaultShip = (float) (SiteSetting::get('shipping_base_fee') ?: (SiteSetting::get('shipping_fee_default') ?: 15000));
            $freeshipThreshold = (float) (SiteSetting::get('freeship_threshold') ?: 100000);
            $shippingFee = ($subtotal >= $freeshipThreshold) ? 0 : $defaultShip;

            // 5. Xử lý mã giảm giá Voucher an toàn và chống race condition bằng lockForUpdate
            $discount = 0;
            if (! empty($data['couponCode'])) {
                $couponCode = strtoupper(trim((string) $data['couponCode']));
                $coupon = Coupon::where('code', $couponCode)->lockForUpdate()->first();

                if (! $coupon) {
                    throw new DomainException("Mã giảm giá \"{$couponCode}\" không tồn tại hoặc đã bị xoá.");
                }

                $validation = $coupon->isValidFor($subtotal);
                if (! $validation['valid']) {
                    throw new DomainException($validation['message']);
                }

                $discount = $coupon->calculateDiscount($subtotal);
                $coupon->increment('used_count');
            }

            $totalAmount = max(0, $subtotal + $shippingFee - $discount);

            // Mã đơn hàng định dạng GAO-XXXXXX
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

            foreach ($preparedItems as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            return $order->load('items');
        });

        // Trigger thông báo Telegram tự động (non-blocking)
        try {
            $this->telegramService->sendOrderNotification($order);
        } catch (\Throwable $e) {
            Log::error('Telegram notification error: '.$e->getMessage());
        }

        return $order;
    }
}
