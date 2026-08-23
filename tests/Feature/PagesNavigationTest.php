<?php

use App\Models\Product;
use App\Models\Sauce;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('home page renders successfully with discovery sections', function () {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('GÀ GIÒN');
    $response->assertSee('SỐT ĐẬM');
    $response->assertSee('4 VỊ SỐT ĐẶC TRƯNG');
    $response->assertSee('MÓN ĐƯỢC GỌI NHIỀU');
    $response->assertSee('ĂN COMBO, LỜI HƠN');
});

test('menu page renders clean catalog without sauce filter section', function () {
    $response = $this->get(route('menu'));

    $response->assertStatus(200);
    $response->assertSee('THỰC ĐƠN ĐẶT MÓN');
    $response->assertSee('Cơm Gà');
    $response->assertDontSee('Lọc theo vị sốt');
});

test('legacy sauce route redirects directly to menu', function () {
    $response = $this->get('/sot');
    $response->assertRedirect(route('menu'));
});

test('product detail page renders complete dish customization options', function () {
    $product = Product::first();
    if ($product) {
        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('THÊM TOPPING HẢO HẠNG (TUỲ CHỌN)');
    }
});

test('order can be placed with standalone sauce and product items', function () {
    $sauce = Sauce::first();
    $product = Product::first();

    $payload = [
        'fullName' => 'Nguyễn Văn A',
        'phone' => '0988888888',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Đường Cầu Giấy',
        'driverNote' => 'Giao trước 12h',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 1,
                'sauce' => 'Sốt Cay Hàn',
                'spiceLevel' => null,
                'toppings' => ['Trứng ốp la'],
            ],
            [
                'item_type' => 'sauce',
                'sauce_id' => $sauce->id,
                'name' => $sauce->name,
                'price' => (float) $sauce->price,
                'quantity' => 2,
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('order_items', [
        'item_type' => 'sauce',
        'product_name' => $sauce->name,
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('order_items', [
        'item_type' => 'product',
        'product_name' => $product->name,
        'quantity' => 1,
    ]);
});
