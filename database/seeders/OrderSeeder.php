<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        OrderItem::truncate();
        Order::truncate();
        Schema::enableForeignKeyConstraints();

        $products = Product::all();
        $sauces = Sauce::all();
        $spiceLevels = SpiceLevel::all();
        $toppings = Topping::all();

        if ($products->isEmpty()) {
            $this->command?->warn('Vui lòng chạy ProductSeeder trước khi chạy OrderSeeder.');

            return;
        }

        $districts = [
            'Quận 1', 'Quận 3', 'Quận 5', 'Quận 7', 'Quận 10', 'Bình Thạnh', 'Phú Nhuận', 'Tân Bình', 'Thủ Đức', 'Cầu Giấy', 'Đống Đa', 'Ba Đình', 'Hai Bà Trưng', 'Thanh Xuân',
        ];

        $customerPool = [
            ['name' => 'Nguyễn Văn Nam', 'phone' => '0901234567', 'district' => 'Quận 1', 'address' => 'Số 12 Lê Lợi, Bến Nghé'],
            ['name' => 'Trần Thị Mai', 'phone' => '0987654321', 'district' => 'Bình Thạnh', 'address' => '120/45 Phan Văn Trị, P.12'],
            ['name' => 'Lê Hoàng Phúc', 'phone' => '0912345678', 'district' => 'Quận 3', 'address' => '88 Nguyễn Thị Minh Khai, P.6'],
            ['name' => 'Phạm Minh Tuấn', 'phone' => '0934567890', 'district' => 'Cầu Giấy', 'address' => 'Số 5 ngõ 165 Cầu Giấy'],
            ['name' => 'Hoàng Thu Thảo', 'phone' => '0978901234', 'district' => 'Đống Đa', 'address' => '45 Chùa Bộc, Quang Trung'],
            ['name' => 'Đặng Quốc Hưng', 'phone' => '0965432109', 'district' => 'Quận 7', 'address' => 'Chung cư Sunrise City, 27 Nguyễn Hữu Thọ'],
            ['name' => 'Vũ Hải Đăng', 'phone' => '0945678901', 'district' => 'Tân Bình', 'address' => '36 Phổ Quang, Phường 2'],
            ['name' => 'Bùi Phương Linh', 'phone' => '0923456789', 'district' => 'Thủ Đức', 'address' => 'Khu đô thị Sala, Mai Chí Thọ'],
            ['name' => 'Đỗ Hữu Nghĩa', 'phone' => '0911223344', 'district' => 'Hai Bà Trưng', 'address' => '102 Bạch Mai'],
            ['name' => 'Ngô Bảo Trâm', 'phone' => '0933445566', 'district' => 'Thanh Xuân', 'address' => '204 Nguyễn Trãi'],
            ['name' => 'Lý Gia Hân', 'phone' => '0908889999', 'district' => 'Phú Nhuận', 'address' => '54 Phan Xích Long, P.2'],
            ['name' => 'Trịnh Công Sơn', 'phone' => '0981122334', 'district' => 'Ba Đình', 'address' => '15 Kim Mã, Ba Đình'],
        ];

        $driverNotes = [
            'Giao trước sảnh toà nhà, gọi trước khi đến 5 phút',
            'Gửi bảo vệ sảnh B giúp em',
            'Bấm chuông tầng 2',
            'Giao hàng tận phòng, phòng 402',
            'Để ở bàn lễ tân, khách tự xuống lấy',
            'Giao cẩn thận tránh làm tràn nước ngọt',
            null,
            null,
        ];

        $itemNotes = [
            'Cho nhiều sốt một chút ạ',
            'Ít cay, không lấy hành tây',
            'Gà chiên giòn rụm giúp mình nhé',
            'Cho nhiều đá vào cốc nước ngọt',
            'Cho thêm tương ớt và khăn ướt',
            null,
            null,
        ];

        $paymentMethods = ['cod', 'momo', 'vnpay', 'zalopay', 'bank_transfer'];

        $now = Carbon::now();
        $ordersCount = 0;

        // 1. Tạo các đơn hàng cho ngày HÔM NAY (trải đều theo khung giờ & trạng thái thực tế)
        $todayOrdersConfig = [
            // Đơn đang chờ xử lý mới đặt
            [
                'time' => $now->copy()->subMinutes(3),
                'status' => 'pending',
                'pay_method' => 'cod',
                'pay_status' => 'pending',
                'cancel_reason' => null,
            ],
            // Đơn đang chờ xử lý gấp (>10 phút)
            [
                'time' => $now->copy()->subMinutes(18),
                'status' => 'pending',
                'pay_method' => 'momo',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            // Đơn đã xác nhận
            [
                'time' => $now->copy()->subMinutes(28),
                'status' => 'confirmed',
                'pay_method' => 'vnpay',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->subMinutes(35),
                'status' => 'confirmed',
                'pay_method' => 'cod',
                'pay_status' => 'pending',
                'cancel_reason' => null,
            ],
            // Đơn đang chuẩn bị
            [
                'time' => $now->copy()->subMinutes(42),
                'status' => 'preparing',
                'pay_method' => 'bank_transfer',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->subMinutes(50),
                'status' => 'preparing',
                'pay_method' => 'cod',
                'pay_status' => 'pending',
                'cancel_reason' => null,
            ],
            // Đơn đang giao
            [
                'time' => $now->copy()->subMinutes(65),
                'status' => 'delivering',
                'pay_method' => 'cod',
                'pay_status' => 'pending',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->subMinutes(80),
                'status' => 'delivering',
                'pay_method' => 'zalopay',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            // Đơn hoàn thành sáng và trưa nay
            [
                'time' => $now->copy()->startOfDay()->setHour(8)->setMinute(30),
                'status' => 'completed',
                'pay_method' => 'cod',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->startOfDay()->setHour(10)->setMinute(15),
                'status' => 'completed',
                'pay_method' => 'momo',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->startOfDay()->setHour(11)->setMinute(45),
                'status' => 'completed',
                'pay_method' => 'vnpay',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->startOfDay()->setHour(12)->setMinute(20),
                'status' => 'completed',
                'pay_method' => 'cod',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            [
                'time' => $now->copy()->startOfDay()->setHour(12)->setMinute(50),
                'status' => 'completed',
                'pay_method' => 'bank_transfer',
                'pay_status' => 'paid',
                'cancel_reason' => null,
            ],
            // Đơn huỷ hôm nay
            [
                'time' => $now->copy()->startOfDay()->setHour(9)->setMinute(10),
                'status' => 'cancelled',
                'pay_method' => 'cod',
                'pay_status' => 'failed',
                'cancel_reason' => 'Khách đổi ý muốn đặt combo khác cho nhóm bạn',
            ],
        ];

        foreach ($todayOrdersConfig as $cfg) {
            $customer = $customerPool[array_rand($customerPool)];
            $this->createSampleOrder($cfg['time'], $cfg['status'], $cfg['pay_method'], $cfg['pay_status'], $cfg['cancel_reason'], $customer, $products, $sauces, $spiceLevels, $toppings, $driverNotes, $itemNotes);
            $ordersCount++;
        }

        // 2. Tạo đơn cho ngày HÔM QUA (Khoảng 8 đơn)
        $yesterday = $now->copy()->subDay();
        $yesterdayHours = [8, 9, 11, 12, 13, 17, 18, 19];
        foreach ($yesterdayHours as $hour) {
            $orderTime = $yesterday->copy()->setHour($hour)->setMinute(rand(5, 55));
            $isCancelled = ($hour === 9);
            $status = $isCancelled ? 'cancelled' : 'completed';
            $payMethod = $paymentMethods[array_rand($paymentMethods)];
            $payStatus = $isCancelled ? 'failed' : 'paid';
            $cancelReason = $isCancelled ? 'Quán hết nguyên liệu gà không xương' : null;
            $customer = $customerPool[array_rand($customerPool)];

            $this->createSampleOrder($orderTime, $status, $payMethod, $payStatus, $cancelReason, $customer, $products, $sauces, $spiceLevels, $toppings, $driverNotes, $itemNotes);
            $ordersCount++;
        }

        // 3. Tạo đơn cho các ngày trong 30 NGÀY QUA (Mỗi ngày 2-5 đơn để biểu đồ và top món cực kỳ chân thực)
        for ($day = 2; $day <= 30; $day++) {
            $targetDate = $now->copy()->subDays($day);
            $dailyOrdersCount = ($targetDate->isWeekend()) ? rand(4, 7) : rand(2, 4);

            for ($i = 0; $i < $dailyOrdersCount; $i++) {
                $orderTime = $targetDate->copy()->setHour(rand(8, 21))->setMinute(rand(0, 59));
                $isCancelled = (rand(1, 10) === 10);
                $status = $isCancelled ? 'cancelled' : 'completed';
                $payMethod = $paymentMethods[array_rand($paymentMethods)];
                $payStatus = $isCancelled ? 'failed' : 'paid';
                $cancelReason = $isCancelled ? 'Khách hàng đặt nhầm địa chỉ giao hàng' : null;
                $customer = $customerPool[array_rand($customerPool)];

                $this->createSampleOrder($orderTime, $status, $payMethod, $payStatus, $cancelReason, $customer, $products, $sauces, $spiceLevels, $toppings, $driverNotes, $itemNotes);
                $ordersCount++;
            }
        }

        $this->command?->info("Đã tạo thành công {$ordersCount} đơn hàng mẫu kèm các món chi tiết!");
    }

    /**
     * Helper tạo 1 đơn hàng và các order_items đi kèm
     */
    private function createSampleOrder(
        Carbon $createdAt,
        string $status,
        string $paymentMethod,
        string $paymentStatus,
        ?string $cancelReason,
        array $customer,
        $products,
        $sauces,
        $spiceLevels,
        $toppings,
        array $driverNotes,
        array $itemNotes
    ): Order {
        $orderCode = 'GAO-'.strtoupper(substr(uniqid(), -6));
        $shippingFee = (rand(1, 4) === 1) ? 0 : 15000;
        $discount = (rand(1, 3) === 1) ? 15000 : 0;

        $order = Order::create([
            'order_code' => $orderCode,
            'customer_name' => $customer['name'],
            'customer_phone' => $customer['phone'],
            'district' => $customer['district'],
            'address' => $customer['address'],
            'driver_note' => $driverNotes[array_rand($driverNotes)],
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'order_status' => $status,
            'cancellation_reason' => $cancelReason,
            'subtotal' => 0,
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total_amount' => 0,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Tạo 1 đến 3 món trong đơn
        $numItems = rand(1, 3);
        $subtotal = 0;

        for ($j = 0; $j < $numItems; $j++) {
            $product = $products->random();
            $qty = rand(1, 2);
            $itemSauce = $sauces->isNotEmpty() ? $sauces->random() : null;
            $itemSpice = $spiceLevels->isNotEmpty() ? $spiceLevels->random()->name : 'Vừa';

            // Chọn ngẫu nhiên 0 - 2 topping
            $selectedToppings = [];
            $toppingPriceSum = 0;
            if ($toppings->isNotEmpty() && rand(0, 1) === 1) {
                $randomToppings = $toppings->random(rand(1, min(2, $toppings->count())));
                foreach ($randomToppings as $top) {
                    $selectedToppings[] = $top->name;
                    $toppingPriceSum += (float) $top->price;
                }
            }

            $unitPrice = (float) $product->price + $toppingPriceSum;
            $totalItemPrice = $unitPrice * $qty;
            $subtotal += $totalItemPrice;

            OrderItem::create([
                'order_id' => $order->id,
                'item_type' => 'product',
                'product_id' => $product->id,
                'sauce_id' => $itemSauce?->id,
                'product_name' => $product->name,
                'price' => $unitPrice,
                'quantity' => $qty,
                'sauce' => $itemSauce?->name,
                'spice_level' => $itemSpice,
                'toppings' => ! empty($selectedToppings) ? $selectedToppings : null,
                'note' => $itemNotes[array_rand($itemNotes)],
                'total_item_price' => $totalItemPrice,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $totalAmount = max(0, $subtotal + $shippingFee - $discount);

        $order->update([
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ]);

        return $order;
    }
}
