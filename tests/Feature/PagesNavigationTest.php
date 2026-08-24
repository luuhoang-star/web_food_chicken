<?php

use App\Models\Benefit;
use App\Models\Combo;
use App\Models\Hero;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Services\OrderService;
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

test('menu page filters popular items with query category=popular', function () {
    $response = $this->get('/menu?category=popular');

    $response->assertStatus(200);
    $response->assertSee('THỰC ĐƠN ĐẶT MÓN');
});

test('product model tag accessor sanitizes legacy tags', function () {
    $productWithNew = new Product(['tag' => 'MỚI']);
    $this->assertNull($productWithNew->tag);

    $productWithLegacy = new Product(['tag' => 'MÓN HOT']);
    $this->assertNull($productWithLegacy->tag);

    $productWithBestSeller = new Product(['tag' => 'BEST SELLER']);
    $this->assertEquals('BEST SELLER', $productWithBestSeller->tag);

    $productWithDiscount = new Product(['tag' => 'TIẾT KIỆM']);
    $this->assertEquals('TIẾT KIỆM', $productWithDiscount->tag);
});

test('order service calculates free shipping for orders over 100k', function () {
    $service = new OrderService;
    $product = Product::first();

    $order = $service->createOrder([
        'fullName' => 'Test Customer',
        'phone' => '0973797151',
        'district' => 'Ga Hà Đông',
        'address' => '123 Test Street',
        'driverNote' => null,
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 120000,
                'quantity' => 1,
            ],
        ],
    ]);

    expect($order->shipping_fee)->toBe('0');
    expect($order->total_amount)->toBe('120000');
    expect($order->order_code)->toStartWith('GAO-');
});

test('combos have items relationship and active scope', function () {
    $combos = Combo::with('items.product')->active()->ordered()->get();
    expect($combos->count())->toBeGreaterThanOrEqual(3);

    $combo2 = $combos->firstWhere('slug', 'combo-2-nguoi');
    expect($combo2)->not->toBeNull();
    expect($combo2->items)->not->toBeEmpty();
    expect($combo2->tag)->toBe('BEST SELLER');
});

test('hero section renders dynamic data from hero model', function () {
    $hero = Hero::active()->ordered()->first();
    expect($hero)->not->toBeNull();
    expect($hero->title)->toBe('GÀ GIÒN.');
    expect($hero->title_highlight)->toBe('SỐT ĐẬM.');

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee($hero->title);
    $response->assertSee($hero->title_highlight);
    $response->assertSee($hero->cta_primary_text);
});

test('benefits section renders dynamic USP items from database', function () {
    $benefits = Benefit::active()->ordered()->get();
    expect($benefits->count())->toBe(3);

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    foreach ($benefits as $benefit) {
        $response->assertSee($benefit->title);
        $response->assertSee($benefit->description);
    }
});

test('testimonials section renders dynamic customer reviews from database', function () {
    $testimonials = Testimonial::active()->ordered()->get();
    expect($testimonials->count())->toBe(3);

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    foreach ($testimonials as $review) {
        $response->assertSee($review->customer_name);
        $response->assertSee($review->location);
    }
});

test('header and footer render dynamic settings from site_settings table', function () {
    $settings = SiteSetting::allKeyed();
    expect($settings)->toHaveKey('hotline');
    expect($settings['hotline'])->toBe('0988.868.GAO');

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee($settings['hotline']);
    $response->assertSee($settings['store_address']);
});

test('quality page renders dynamic commitments and sauces from database', function () {
    $response = $this->get(route('quality'));
    $response->assertStatus(200);
    $response->assertSee('CAM KẾT CHẤT LƯỢNG');
    $response->assertSee('3 TIÊU CHUẨN PHỤC VỤ HÀNG ĐẦU');
});

test('floating contact widget renders messenger and zalo chat buttons', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('zalo.me/0973797151');
    $response->assertSee('m.me/luuhoang.it');
});

test('order tracking page renders search form and intro highlights', function () {
    $response = $this->get(route('order.tracking'));
    $response->assertStatus(200);
    $response->assertSee('TRA CỨU');
    $response->assertSee('ĐƠN HÀNG');
    $response->assertSee('Giao Hàng 25–40 Phút');
});

test('order tracking can find order by order code or phone number', function () {
    $service = new OrderService;
    $product = Product::first();

    $order = $service->createOrder([
        'fullName' => 'Khách Hàng Test',
        'phone' => '0973797151',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Đường Cầu Giấy',
        'driverNote' => 'Giao trước 12h',
        'paymentMethod' => 'cod',
        'items' => [
            [
                'item_type' => 'product',
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => 75000,
                'quantity' => 1,
            ],
        ],
    ]);

    // 1. Tìm bằng mã đơn
    $responseCode = $this->get(route('order.tracking', ['code' => $order->order_code]));
    $responseCode->assertStatus(200);
    $responseCode->assertSee($order->order_code);
    $responseCode->assertSee('Khách Hàng Test');
    $responseCode->assertSee('Đã đặt đơn');

    // 2. Tìm bằng số điện thoại
    $responsePhone = $this->get(route('order.tracking', ['phone' => '0973797151']));
    $responsePhone->assertStatus(200);
    $responsePhone->assertSee($order->order_code);
    $responsePhone->assertSee('123 Đường Cầu Giấy');
});

test('order tracking displays friendly empty state when order not found', function () {
    $response = $this->get(route('order.tracking', ['q' => 'NON_EXISTENT_ORDER_99999']));
    $response->assertStatus(200);
    $response->assertSee('Không tìm thấy đơn hàng!');
    $response->assertSee('NON_EXISTENT_ORDER_99999');
});
