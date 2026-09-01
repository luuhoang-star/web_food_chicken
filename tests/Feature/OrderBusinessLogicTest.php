<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Topping;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    SiteSetting::updateOrCreate(['key' => 'store_open_status'], ['value' => 'open']);
    SiteSetting::updateOrCreate(['key' => 'shipping_fee_default'], ['value' => '15000']);
    SiteSetting::updateOrCreate(['key' => 'freeship_threshold'], ['value' => '100000']);
});

test('order is created successfully with official database prices and toppings', function () {
    $cat = Category::create(['name' => 'Cơm Gà', 'slug' => 'rice', 'order' => 1]);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Cơm Gà Sốt Cay Hàn',
        'slug' => 'com-ga-sot-cay-han',
        'price' => 50000,
        'is_available' => true,
    ]);

    $topping = Topping::create([
        'name' => 'Trứng Ốp La',
        'price' => 10000,
        'is_active' => true,
    ]);

    $payload = [
        'fullName' => 'Nguyễn Văn Test',
        'phone' => '0987654321',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Đường Cầu Giấy',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 1000, // Spoofed client price
                'quantity' => 2,
                'sauce' => 'Sốt Cay Hàn',
                'toppings' => [$topping->name],
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Đặt hàng thành công!',
        ]);

    // Expected per item: 50,000 + 10,000 = 60,000. Qty 2 -> subtotal = 120,000
    // Subtotal >= 100,000 freeship -> shipping = 0, total = 120,000
    $order = Order::with('items')->first();
    expect($order)->not->toBeNull();
    expect((float) $order->subtotal)->toEqual(120000.0);
    expect((float) $order->shipping_fee)->toEqual(0.0);
    expect((float) $order->total_amount)->toEqual(120000.0);
    expect((float) $order->items->first()->price)->toEqual(60000.0);
    expect((float) $order->items->first()->total_item_price)->toEqual(120000.0);
});

test('order creation ignores arbitrary discount injection without coupon', function () {
    $cat = Category::create(['name' => 'Cơm Gà', 'slug' => 'rice', 'order' => 1]);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Cơm Gà Đùi',
        'slug' => 'com-ga-dui',
        'price' => 60000,
        'is_available' => true,
    ]);

    $payload = [
        'fullName' => 'Khách Ăn Thử',
        'phone' => '0912345678',
        'district' => 'Quận Đống Đa',
        'address' => '45 Chùa Láng',
        'paymentMethod' => 'cod',
        'discount' => 50000, // Injected discount without coupon code
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 60000,
                'quantity' => 1,
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);
    $response->assertStatus(201);

    $order = Order::first();
    // Subtotal = 60,000, Ship = 15,000, Discount must be 0 -> Total = 75,000
    expect((float) $order->discount)->toEqual(0.0);
    expect((float) $order->shipping_fee)->toEqual(15000.0);
    expect((float) $order->total_amount)->toEqual(75000.0);
});

test('order creation calculates discount from valid coupon and increments usage', function () {
    $cat = Category::create(['name' => 'Cơm Gà', 'slug' => 'rice', 'order' => 1]);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Cơm Gà Sốt Bơ Tỏi',
        'slug' => 'com-ga-sot-bo-toi',
        'price' => 50000,
        'is_available' => true,
    ]);

    $coupon = Coupon::create([
        'code' => 'GIAM20K',
        'name' => 'Giảm 20K cho đơn từ 100K',
        'type' => 'fixed',
        'value' => 20000,
        'min_order_amount' => 100000,
        'usage_limit' => 5,
        'used_count' => 0,
        'is_active' => true,
    ]);

    $payload = [
        'fullName' => 'Trần Văn B',
        'phone' => '0933221100',
        'district' => 'Quận Ba Đình',
        'address' => '10 Kim Mã',
        'paymentMethod' => 'cod',
        'couponCode' => 'GIAM20K',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 50000,
                'quantity' => 2, // Subtotal = 100,000
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);
    $response->assertStatus(201);

    $order = Order::first();
    expect((float) $order->discount)->toEqual(20000.0);
    expect((float) $order->total_amount)->toEqual(80000.0); // 100k subtotal - 20k discount + 0k ship (freeship >= 100k)

    $coupon->refresh();
    expect($coupon->used_count)->toBe(1);
});

test('order creation is blocked when kitchen is paused', function () {
    SiteSetting::updateOrCreate(['key' => 'store_open_status'], ['value' => 'paused']);

    $cat = Category::create(['name' => 'Cơm Gà', 'slug' => 'rice', 'order' => 1]);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Cơm Gà',
        'slug' => 'com-ga',
        'price' => 45000,
        'is_available' => true,
    ]);

    $payload = [
        'fullName' => 'Người Đặt Khi Bếp Đóng',
        'phone' => '0988776655',
        'district' => 'Quận Cầu Giấy',
        'address' => 'Số 1 Trần Duy Hưng',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 45000,
                'quantity' => 1,
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);
    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    expect(Order::count())->toBe(0);
});

test('order creation rejects unavailable items', function () {
    $cat = Category::create(['name' => 'Cơm Gà', 'slug' => 'rice', 'order' => 1]);
    $product = Product::create([
        'category_id' => $cat->id,
        'name' => 'Món Hết Hàng',
        'slug' => 'mon-het-hang',
        'price' => 50000,
        'is_available' => false, // Out of stock
    ]);

    $payload = [
        'fullName' => 'Khách Hàng',
        'phone' => '0988776655',
        'district' => 'Quận Cầu Giấy',
        'address' => 'Cầu Giấy',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 50000,
                'quantity' => 1,
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);
    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);
});

test('order tracking requires exact code or full phone number to protect customer privacy', function () {
    $order = Order::create([
        'order_code' => 'GAO-SECRET',
        'customer_name' => 'Khách VIP',
        'customer_phone' => '0973797151',
        'district' => 'Quận Cầu Giấy',
        'address' => '100 Trần Thái Tông',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'order_status' => 'pending',
        'subtotal' => 50000,
        'shipping_fee' => 15000,
        'discount' => 0,
        'total_amount' => 65000,
    ]);

    // 1. Short substring query '09' should NOT match
    $res1 = $this->get(route('order.tracking', ['q' => '09']));
    $res1->assertStatus(200);
    $res1->assertViewHas('activeOrder', null);

    // 2. Exact phone search should match
    $res2 = $this->get(route('order.tracking', ['q' => '0973797151']));
    $res2->assertStatus(200);
    $res2->assertViewHas('activeOrder');
    expect($res2->viewData('activeOrder')->id)->toBe($order->id);

    // 3. Exact order code search should match
    $res3 = $this->get(route('order.tracking', ['q' => 'GAO-SECRET']));
    $res3->assertStatus(200);
    $res3->assertViewHas('activeOrder');
    expect($res3->viewData('activeOrder')->id)->toBe($order->id);
});
