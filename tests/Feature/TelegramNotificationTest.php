<?php

use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    config([
        'services.telegram.bot_token' => '123456:TEST_BOT_TOKEN',
        'services.telegram.chat_id' => '987654321',
        'services.telegram.enabled' => true,
    ]);
});

test('telegram service sends order notification with formatted html message', function () {
    Http::fake([
        'https://api.telegram.org/bot123456:TEST_BOT_TOKEN/sendMessage' => Http::response(['ok' => true], 200),
    ]);

    $service = new OrderService;
    $product = Product::first();

    $order = $service->createOrder([
        'fullName' => 'Nguyễn Văn Bếp',
        'phone' => '0973797151',
        'district' => 'Quận Đống Đa',
        'address' => '456 Tây Sơn',
        'driverNote' => 'Gọi trước khi đến',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 79000,
                'quantity' => 2,
                'sauce' => 'Sốt Cay Hàn',
                'spiceLevel' => 'Cay vừa',
                'toppings' => ['Trứng ốp la', 'Phô mai'],
            ],
        ],
    ]);

    Http::assertSent(function ($request) use ($order) {
        return str_contains($request->url(), 'sendMessage')
            && $request['chat_id'] === '987654321'
            && str_contains($request['text'], $order->order_code)
            && str_contains($request['text'], 'Nguyễn Văn Bếp')
            && str_contains($request['text'], '0973797151')
            && str_contains($request['text'], 'Sốt Cay Hàn');
    });
});

test('order creation succeeds even if telegram api fails (failsafe)', function () {
    Http::fake([
        'https://api.telegram.org/*' => Http::response(['ok' => false, 'description' => 'Unauthorized'], 401),
    ]);

    $service = new OrderService;
    $product = Product::first();

    $order = $service->createOrder([
        'fullName' => 'Khách Hàng Failsafe',
        'phone' => '0988888888',
        'district' => 'Quận Cầu Giấy',
        'address' => '789 Cầu Giấy',
        'driverNote' => null,
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 85000,
                'quantity' => 1,
            ],
        ],
    ]);

    expect($order)->not->toBeNull();
    expect($order->customer_name)->toBe('Khách Hàng Failsafe');
    $this->assertDatabaseHas('orders', ['customer_phone' => '0988888888']);
});

test('artisan telegram:test command sends test message successfully', function () {
    Http::fake([
        'https://api.telegram.org/bot123456:TEST_BOT_TOKEN/sendMessage' => Http::response(['ok' => true], 200),
    ]);

    $this->artisan('telegram:test')
        ->expectsOutputToContain('Đã gửi tin nhắn test thành công')
        ->assertSuccessful();
});
