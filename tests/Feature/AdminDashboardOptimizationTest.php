<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@gao.vn',
    ]);
});

test('dashboard calculates urgent orders threshold and completion rate correctly', function () {
    // 1. Đơn cũ chờ quá 15 phút (khẩn cấp)
    $urgentOrder = Order::create([
        'order_code' => 'GAO-URGENT',
        'customer_name' => 'Khách Chờ Lâu',
        'customer_phone' => '0912345678',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Đường Cầu Giấy',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'order_status' => 'pending',
        'subtotal' => 60000,
        'shipping_fee' => 15000,
        'total_amount' => 75000,
    ]);
    $urgentOrder->created_at = now()->subMinutes(15);
    $urgentOrder->saveQuietly();

    // 2. Đơn mới tạo 2 phút trước (chưa khẩn cấp)
    $freshOrder = Order::create([
        'order_code' => 'GAO-FRESH',
        'customer_name' => 'Khách Vừa Đặt',
        'customer_phone' => '0987654321',
        'district' => 'Quận Đống Đa',
        'address' => '456 Phố Chùa Láng',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'order_status' => 'pending',
        'subtotal' => 50000,
        'shipping_fee' => 15000,
        'total_amount' => 65000,
    ]);
    $freshOrder->created_at = now()->subMinutes(2);
    $freshOrder->saveQuietly();

    // 3. Đơn đã hoàn thành
    $doneOrder = Order::create([
        'order_code' => 'GAO-DONE',
        'customer_name' => 'Khách Đã Nhận',
        'customer_phone' => '0933221100',
        'district' => 'Quận Ba Đình',
        'address' => '789 Phố Kim Mã',
        'payment_method' => 'cod',
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'subtotal' => 100000,
        'shipping_fee' => 0,
        'total_amount' => 100000,
    ]);
    $doneOrder->created_at = now()->subMinutes(30);
    $doneOrder->saveQuietly();

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard', ['range' => 'today']));

    $response->assertStatus(200);
    $response->assertViewHas('urgentOrdersCount', 1);
    $response->assertViewHas('pendingOrdersCount', 2);
    $response->assertViewHas('completedOrdersCount', 1);
    $response->assertViewHas('periodOrdersCount', 3);
    $response->assertViewHas('completionRate', 33);
});

test('admin can cancel order with custom reason and reason is saved in database', function () {
    $order = Order::create([
        'order_code' => 'GAO-CANCEL',
        'customer_name' => 'Khách Huỷ',
        'customer_phone' => '0977665544',
        'district' => 'Quận Cầu Giấy',
        'address' => '99 Phố Duy Tân, Cầu Giấy',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'order_status' => 'pending',
        'subtotal' => 80000,
        'shipping_fee' => 15000,
        'total_amount' => 95000,
    ]);

    $response = $this->actingAs($this->admin)->patchJson(route('admin.orders.update-status', $order->id), [
        'order_status' => 'cancelled',
        'cancellation_reason' => 'Quán hết đùi gà sốt cay',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'order_status' => 'cancelled',
            'cancellation_reason' => 'Quán hết đùi gà sốt cay',
        ]);

    $order->refresh();
    expect($order->order_status)->toBe('cancelled');
    expect($order->cancellation_reason)->toBe('Quán hết đùi gà sốt cay');
});

test('cannot change status of already completed order', function () {
    $completedOrder = Order::create([
        'order_code' => 'GAO-COMPLETED-LOCKED',
        'customer_name' => 'Khách Đã Nhận Đơn',
        'customer_phone' => '0912345678',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Cầu Giấy, Hà Nội',
        'payment_method' => 'cod',
        'payment_status' => 'paid',
        'order_status' => 'completed',
        'subtotal' => 120000,
        'shipping_fee' => 0,
        'total_amount' => 120000,
    ]);

    $response = $this->actingAs($this->admin)->patchJson(route('admin.orders.update-status', $completedOrder->id), [
        'order_status' => 'delivering',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    $completedOrder->refresh();
    expect($completedOrder->order_status)->toBe('completed');
});

test('cannot change status of already cancelled order', function () {
    $cancelledOrder = Order::create([
        'order_code' => 'GAO-CANCELLED-LOCKED',
        'customer_name' => 'Khách Đã Huỷ Đơn',
        'customer_phone' => '0987654321',
        'district' => 'Quận Đống Đa',
        'address' => '456 Chùa Láng, Hà Nội',
        'payment_method' => 'cod',
        'payment_status' => 'pending',
        'order_status' => 'cancelled',
        'cancellation_reason' => 'Khách đổi ý không muốn ăn',
        'subtotal' => 90000,
        'shipping_fee' => 15000,
        'total_amount' => 105000,
    ]);

    $response = $this->actingAs($this->admin)->patchJson(route('admin.orders.update-status', $cancelledOrder->id), [
        'order_status' => 'preparing',
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    $cancelledOrder->refresh();
    expect($cancelledOrder->order_status)->toBe('cancelled');
});
