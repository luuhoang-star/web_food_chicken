<?php

use App\Models\Benefit;
use App\Models\Category;
use App\Models\Combo;
use App\Models\Coupon;
use App\Models\Hero;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\Testimonial;
use App\Models\Topping;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('unauthenticated users are redirected from admin routes to login page', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('admin.login'));
});

test('admin can view login page and authenticate successfully', function () {
    $response = $this->get(route('admin.login'));
    $response->assertStatus(200);
    $response->assertSee('GAO ADMIN');

    $loginResponse = $this->post(route('admin.login.submit'), [
        'email' => 'admin@gao.vn',
        'password' => 'admin123',
    ]);

    $loginResponse->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
});

test('admin can access dashboard and view kpi statistics', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSee('Doanh thu');
    $response->assertSee('Đơn cần xử lý');
    $response->assertSee('Giá trị đơn TB');
});

test('admin can filter dashboard by date range', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->get(route('admin.dashboard', ['range' => 'today']));
    $response->assertStatus(200);
    $response->assertSee('Hôm nay');

    $response30 = $this->actingAs($admin)->get(route('admin.dashboard', ['range' => '30days']));
    $response30->assertStatus(200);
});

test('admin can change order status and it reflects across system', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();
    $service = new OrderService;
    $product = Product::first();

    $order = $service->createOrder([
        'fullName' => 'Khách Hàng Thử Nghiệm',
        'phone' => '0973797151',
        'district' => 'Quận Cầu Giấy',
        'address' => '123 Cầu Giấy',
        'driverNote' => null,
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

    expect($order->order_status)->toBe('pending');

    // Chuyển sang đang chuẩn bị
    $updateResponse = $this->actingAs($admin)->patch(route('admin.orders.update-status', $order->id), [
        'order_status' => 'preparing',
    ]);

    $updateResponse->assertRedirect();
    $order->refresh();
    expect($order->order_status)->toBe('preparing');
    expect($order->status_label)->toBe('Đang chuẩn bị');

    // Chuyển sang đang giao qua JSON
    $deliveringResponse = $this->actingAs($admin)->patchJson(route('admin.orders.update-status', $order->id), [
        'order_status' => 'delivering',
    ]);
    $deliveringResponse->assertStatus(200)->assertJson(['success' => true, 'order_status' => 'delivering']);
    $order->refresh();
    expect($order->order_status)->toBe('delivering');

    // Chuyển sang hoàn thành qua JSON
    $completeResponse = $this->actingAs($admin)->patchJson(route('admin.orders.update-status', $order->id), [
        'order_status' => 'completed',
    ]);

    $completeResponse->assertStatus(200)->assertJson(['success' => true, 'order_status' => 'completed', 'is_paid' => true]);
    $order->refresh();
    expect($order->order_status)->toBe('completed');
    expect($order->payment_status)->toBe('paid');
});

test('admin can create, edit and delete product', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();
    $category = Category::first();
    $sauce = Sauce::first();

    // 1. Xem form create
    $createView = $this->actingAs($admin)->get(route('admin.products.create'));
    $createView->assertStatus(200);
    $createView->assertSee('Thêm Món Ăn Mới');

    // 2. Tạo món mới
    $storeResponse = $this->actingAs($admin)->post(route('admin.products.store'), [
        'name' => 'Gà Rán Phô Mai Siêu Cay',
        'category_id' => $category->id,
        'sauce_id' => $sauce->id,
        'sauce_selection' => 'fixed',
        'price' => 69000,
        'original_price' => 79000,
        'tag' => 'BEST SELLER',
        'subtag' => '🍗 Giòn rụm béo ngậy',
        'description' => 'Mô tả món gà rán sốt phô mai đặc biệt.',
        'is_available' => '1',
    ]);

    $storeResponse->assertRedirect(route('admin.products.index'));
    $newProduct = Product::where('name', 'Gà Rán Phô Mai Siêu Cay')->first();
    expect($newProduct)->not->toBeNull();
    expect((int) $newProduct->price)->toBe(69000);

    // 3. Xem form edit
    $editView = $this->actingAs($admin)->get(route('admin.products.edit', $newProduct->id));
    $editView->assertStatus(200);
    $editView->assertSee('Gà Rán Phô Mai Siêu Cay');

    // 4. Update món
    $updateResponse = $this->actingAs($admin)->put(route('admin.products.update', $newProduct->id), [
        'name' => 'Gà Rán Phô Mai Siêu Cay Đặc Biệt',
        'category_id' => $category->id,
        'sauce_id' => $sauce->id,
        'sauce_selection' => 'fixed',
        'price' => 72000,
        'is_available' => '1',
    ]);

    $updateResponse->assertRedirect(route('admin.products.index'));
    $newProduct->refresh();
    expect($newProduct->name)->toBe('Gà Rán Phô Mai Siêu Cay Đặc Biệt');
    expect((int) $newProduct->price)->toBe(72000);

    // 5. Xoá món
    $deleteResponse = $this->actingAs($admin)->delete(route('admin.products.destroy', $newProduct->id));
    $deleteResponse->assertRedirect(route('admin.products.index'));
    expect(Product::where('id', $newProduct->id)->exists())->toBeFalse();
});

test('admin can update dish price via json and toggle availability via json', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();
    $product = Product::first();

    // 1. Cập nhật giá bán qua JSON (tự lưu)
    $priceResponse = $this->actingAs($admin)->patchJson(route('admin.products.update-price', $product->id), [
        'price' => 89000,
    ]);

    $priceResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'price' => 89000,
        ]);
    $product->refresh();
    expect((int) $product->price)->toBe(89000);

    // 2. Bật / Tắt trạng thái mở bán qua JSON
    $initialStatus = $product->is_available;
    $toggleResponse = $this->actingAs($admin)->patchJson(route('admin.products.toggle-availability', $product->id));

    $toggleResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'is_available' => ! $initialStatus,
        ]);
    $product->refresh();
    expect($product->is_available)->toBe(! $initialStatus);
});

test('admin can filter, sort and perform bulk actions on products', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Lọc và sắp xếp
    $response = $this->actingAs($admin)->get(route('admin.products.index', [
        'status' => 'available',
        'sort' => 'best_seller',
        'per_page' => 30,
    ]));
    $response->assertStatus(200);

    // 2. Bulk action: Chuyển hàng loạt sang hết món
    $products = Product::take(2)->get();
    $ids = $products->pluck('id')->toArray();

    $bulkResponse = $this->actingAs($admin)->post(route('admin.products.bulk-action'), [
        'ids' => $ids,
        'action' => 'out_of_stock',
    ]);
    $bulkResponse->assertRedirect();

    foreach ($ids as $id) {
        expect(Product::find($id)->is_available)->toBeFalse();
    }

    // 3. Bulk action: Mở bán lại hàng loạt
    $bulkAvailable = $this->actingAs($admin)->post(route('admin.products.bulk-action'), [
        'ids' => $ids,
        'action' => 'available',
    ]);
    $bulkAvailable->assertRedirect();

    foreach ($ids as $id) {
        expect(Product::find($id)->is_available)->toBeTrue();
    }
});

test('admin can create, update, toggle and delete categories', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Index categories
    $indexResponse = $this->actingAs($admin)->get(route('admin.categories.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Danh Mục Món Ăn');

    // 2. Thêm mới danh mục
    $storeResponse = $this->actingAs($admin)->post(route('admin.categories.store'), [
        'name' => 'Món Ăn Vặt Giòn Cay',
        'icon' => '🍟',
        'order' => 10,
    ]);
    $storeResponse->assertRedirect();
    $category = Category::where('name', 'Món Ăn Vặt Giòn Cay')->first();
    expect($category)->not->toBeNull();
    expect($category->icon)->toBe('🍟');

    // 3. Update danh mục
    $updateResponse = $this->actingAs($admin)->put(route('admin.categories.update', $category->id), [
        'name' => 'Ăn Vặt & Tráng Miệng',
        'icon' => '🍧',
        'order' => 12,
    ]);
    $updateResponse->assertRedirect();
    $category->refresh();
    expect($category->name)->toBe('Ăn Vặt & Tráng Miệng');
    expect($category->icon)->toBe('🍧');

    // 4. Toggle active
    $toggleResponse = $this->actingAs($admin)->patch(route('admin.categories.toggle', $category->id));
    $toggleResponse->assertRedirect();
    $category->refresh();
    expect($category->is_active)->toBeFalse();

    // 5. Delete danh mục (khi không có món)
    $deleteResponse = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));
    $deleteResponse->assertRedirect();
    expect(Category::where('id', $category->id)->exists())->toBeFalse();
});

test('admin can create, update, toggle and delete combos', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();
    $product = Product::first();

    // 1. View combos index
    $indexResponse = $this->actingAs($admin)->get(route('admin.combos.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Combo Món Ăn');
    $indexResponse->assertSee('Thêm Combo Mới');

    // 2. View create form
    $createResponse = $this->actingAs($admin)->get(route('admin.combos.create'));
    $createResponse->assertStatus(200);
    $createResponse->assertSee('Tạo Combo Món Ưu Đãi Mới');

    // 3. Store new combo
    $storeResponse = $this->actingAs($admin)->post(route('admin.combos.store'), [
        'name' => 'Combo Siêu Gà Tiệc Tùng',
        'price' => 199000,
        'original_price' => 250000,
        'tag' => 'BEST SELLER',
        'subtag' => '🍱 Dành cho nhóm 4-5 người',
        'description' => 'Gói tiệc gà rán đầy ắp với sốt tuỳ chọn.',
        'items' => [
            ['item_name' => '2 Cơm gà sốt', 'quantity' => 2, 'product_id' => $product->id],
            ['item_name' => '2 Coca lon', 'quantity' => 2, 'product_id' => null],
        ],
    ]);
    $storeResponse->assertRedirect(route('admin.combos.index'));
    $combo = Combo::where('name', 'Combo Siêu Gà Tiệc Tùng')->first();
    expect($combo)->not->toBeNull();
    expect((int) $combo->price)->toBe(199000);
    expect($combo->items()->count())->toBe(2);

    // 4. Edit combo
    $editResponse = $this->actingAs($admin)->get(route('admin.combos.edit', $combo->id));
    $editResponse->assertStatus(200);
    $editResponse->assertSee('Combo Siêu Gà Tiệc Tùng');

    // 5. Update combo
    $updateResponse = $this->actingAs($admin)->put(route('admin.combos.update', $combo->id), [
        'name' => 'Combo Siêu Gà Tiệc Tùng VIP',
        'price' => 210000,
        'original_price' => 280000,
        'tag' => 'TIẾT KIỆM',
        'subtag' => '🍱 Gói VIP 5 người',
        'items' => [
            ['item_name' => '3 Cơm gà sốt', 'quantity' => 3, 'product_id' => $product->id],
        ],
    ]);
    $updateResponse->assertRedirect(route('admin.combos.index'));
    $combo->refresh();
    expect($combo->name)->toBe('Combo Siêu Gà Tiệc Tùng VIP');
    expect((int) $combo->price)->toBe(210000);
    expect($combo->items()->count())->toBe(1);

    // 6. Update combo price via JSON (Inline price edit)
    $priceResponse = $this->actingAs($admin)->patchJson(route('admin.combos.update-price', $combo->id), [
        'price' => 225000,
    ]);
    $priceResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'price' => 225000,
        ]);
    $combo->refresh();
    expect((int) $combo->price)->toBe(225000);

    // 7. Toggle combo
    $toggleResponse = $this->actingAs($admin)->patch(route('admin.combos.toggle', $combo->id));
    $toggleResponse->assertRedirect();
    $combo->refresh();
    expect($combo->is_active)->toBeFalse();

    // 8. Delete combo
    $deleteResponse = $this->actingAs($admin)->delete(route('admin.combos.destroy', $combo->id));
    $deleteResponse->assertRedirect();
    expect(Combo::where('id', $combo->id)->exists())->toBeFalse();
});

test('admin can create, update, toggle and delete coupons and apply via api', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Index coupons
    $indexResponse = $this->actingAs($admin)->get(route('admin.coupons.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Quản Lý Voucher');
    $indexResponse->assertSee('Tạo voucher mới');

    // 2. Tạo mã giảm giá mới
    $storeResponse = $this->actingAs($admin)->post(route('admin.coupons.store'), [
        'code' => 'GAO25K',
        'name' => 'Ưu đãi đặt đơn đầu tuần',
        'type' => 'fixed',
        'value' => 25000,
        'min_order_amount' => 100000,
    ]);
    $storeResponse->assertRedirect();
    $coupon = Coupon::where('code', 'GAO25K')->first();
    expect($coupon)->not->toBeNull();
    expect((int) $coupon->value)->toBe(25000);

    // 3. Apply coupon API success
    $applyResponse = $this->postJson(route('coupons.apply'), [
        'code' => 'GAO25K',
        'subtotal' => 120000,
    ]);
    $applyResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'coupon_code' => 'GAO25K',
            'discount_amount' => 25000,
        ]);

    // 4. Apply coupon API fail (under min order amount)
    $failResponse = $this->postJson(route('coupons.apply'), [
        'code' => 'GAO25K',
        'subtotal' => 80000,
    ]);
    $failResponse->assertStatus(422)
        ->assertJson([
            'success' => false,
        ]);

    // 5. Toggle coupon
    $toggleResponse = $this->actingAs($admin)->patch(route('admin.coupons.toggle', $coupon->id));
    $toggleResponse->assertRedirect();
    $coupon->refresh();
    expect($coupon->is_active)->toBeFalse();

    // 6. Delete coupon
    $deleteResponse = $this->actingAs($admin)->delete(route('admin.coupons.destroy', $coupon->id));
    $deleteResponse->assertRedirect();
    expect(Coupon::where('id', $coupon->id)->exists())->toBeFalse();
});

test('admin can query check-new orders api for audio alerts', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->get(route('admin.orders.check-new', ['last_order_id' => 0]));
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'current_max_id',
        'has_new',
        'pending_total',
    ]);
});

test('admin can update sauce price and toggle toppings', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();
    $sauce = Sauce::first();
    $topping = Topping::first();

    // 1. Sửa sốt qua JSON
    $sauceResponse = $this->actingAs($admin)->patchJson(route('admin.sauces.update', $sauce->id), [
        'tagline' => 'Đậm đà chuẩn vị',
        'price' => 15000,
    ]);
    $sauceResponse->assertStatus(200)->assertJson(['success' => true]);
    $sauce->refresh();
    expect((int) $sauce->price)->toBe(15000);

    // 2. Bật/Tắt topping qua JSON
    $initialToppingStatus = $topping->is_available;
    $toppingResponse = $this->actingAs($admin)->patchJson(route('admin.toppings.toggle', $topping->id));
    $toppingResponse->assertStatus(200)->assertJson(['success' => true, 'is_available' => ! $initialToppingStatus]);
    $topping->refresh();
    expect($topping->is_available)->toBe(! $initialToppingStatus);

    // 3. Sửa giá topping qua JSON
    $toppingPriceResponse = $this->actingAs($admin)->patchJson(route('admin.toppings.update', $topping->id), [
        'price' => 18000,
    ]);
    $toppingPriceResponse->assertStatus(200)->assertJson(['success' => true]);
    $topping->refresh();
    expect((int) $topping->price)->toBe(18000);
});

test('admin can update homepage banner and manage reviews', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Cập nhật Hero Banner
    $heroResponse = $this->actingAs($admin)->post(route('admin.content.hero.update'), [
        'badge' => 'Thương hiệu uy tín',
        'title' => 'GÀ GIÒN NÓNG HỔI',
        'title_highlight' => 'SỐT ĐẬM ĐÀ',
        'description' => 'Món ngon mỗi ngày cho bạn.',
        'stat_number' => '20.000+',
        'stat_label' => 'suất ăn đã phục vụ',
    ]);
    $heroResponse->assertRedirect();
    $hero = Hero::first();
    expect($hero->title)->toBe('GÀ GIÒN NÓNG HỔI');

    // 2. Cập nhật Benefit qua JSON
    $benefit = Benefit::first();
    if ($benefit) {
        $benefitResponse = $this->actingAs($admin)->patchJson(route('admin.content.benefit.update', $benefit->id), [
            'title' => 'Giao Hỏa Tốc 20P',
            'description' => 'Nóng giòn tận cửa',
        ]);
        $benefitResponse->assertStatus(200)->assertJson(['success' => true]);
        $benefit->refresh();
        expect($benefit->title)->toBe('Giao Hỏa Tốc 20P');
    }

    // 3. Thêm Review mới qua JSON
    $reviewResponse = $this->actingAs($admin)->postJson(route('admin.content.testimonial.store'), [
        'customer_name' => 'Nguyễn Minh Quân',
        'location' => 'Quận Ba Đình',
        'rating' => 5,
        'comment' => 'Gà rất giòn và sốt bơ tỏi thơm lừng!',
        'favorite_dish' => 'Cơm Gà Sốt Bơ Tỏi',
    ]);
    $reviewResponse->assertStatus(200)->assertJson(['success' => true]);
    $testimonial = Testimonial::where('customer_name', 'Nguyễn Minh Quân')->first();
    expect($testimonial)->not->toBeNull();

    // 4. Cập nhật Review qua JSON
    $updateReviewResponse = $this->actingAs($admin)->putJson(route('admin.content.testimonial.update', $testimonial->id), [
        'customer_name' => 'Nguyễn Minh Quân VIP',
        'location' => 'Quận Cầu Giấy',
        'rating' => 5,
        'content' => 'Gà rất giòn và sốt cực ngon!',
        'favorite_dish' => 'Cơm Gà Sốt Cay Hàn',
    ]);
    $updateReviewResponse->assertStatus(200)->assertJson(['success' => true]);
    $testimonial->refresh();
    expect($testimonial->customer_name)->toBe('Nguyễn Minh Quân VIP');

    // 5. Xoá Review qua JSON
    $deleteReviewResponse = $this->actingAs($admin)->deleteJson(route('admin.content.testimonial.delete', $testimonial->id));
    $deleteReviewResponse->assertStatus(200)->assertJson(['success' => true]);
    expect(Testimonial::where('id', $testimonial->id)->exists())->toBeFalse();
});

test('admin can update comprehensive site settings', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
        'hotline' => '0911.222.333',
        'location_badge' => 'Hà Nội (Bán kính 5km)',
        'shipping_fee_default' => '20000',
        'freeship_threshold' => '150000',
        'bank_name' => 'Techcombank',
        'bank_account_number' => '1903888888',
        'bank_account_holder' => 'GAO CHICKEN',
        'popup_enabled' => '1',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('site_settings', [
        'key' => 'hotline',
        'value' => '0911.222.333',
    ]);
    $this->assertDatabaseHas('site_settings', [
        'key' => 'bank_name',
        'value' => 'Techcombank',
    ]);
    $this->assertDatabaseHas('site_settings', [
        'key' => 'popup_enabled',
        'value' => '1',
    ]);
});

test('admin can toggle store status between open and paused', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Chuyển sang tạm dừng nhận đơn
    $pauseResponse = $this->actingAs($admin)->patch(route('admin.settings.toggle-store-status'));
    $pauseResponse->assertRedirect();
    $this->assertDatabaseHas('site_settings', [
        'key' => 'store_open_status',
        'value' => 'paused',
    ]);

    // 2. Chuyển lại sang mở bếp
    $openResponse = $this->actingAs($admin)->patch(route('admin.settings.toggle-store-status'));
    $openResponse->assertRedirect();
    $this->assertDatabaseHas('site_settings', [
        'key' => 'store_open_status',
        'value' => 'open',
    ]);
});

test('admin can view profile, update name and email, and change password', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    // 1. Xem trang profile
    $profileView = $this->actingAs($admin)->get(route('admin.profile.show'));
    $profileView->assertStatus(200);
    $profileView->assertSee('Thông Tin Tài Khoản');
    $profileView->assertSee('Đổi Mật Khẩu Mới');

    // 2. Cập nhật họ tên và email
    $updateProfile = $this->actingAs($admin)->put(route('admin.profile.update'), [
        'name' => 'Chủ Quán GAO Mới',
        'email' => 'chuquan@gao.vn',
    ]);
    $updateProfile->assertRedirect();
    $admin->refresh();
    expect($admin->name)->toBe('Chủ Quán GAO Mới');
    expect($admin->email)->toBe('chuquan@gao.vn');

    // 3. Đổi mật khẩu
    $updatePassword = $this->actingAs($admin)->put(route('admin.profile.password'), [
        'current_password' => 'admin123',
        'password' => 'newpassword888',
        'password_confirmation' => 'newpassword888',
    ]);
    $updatePassword->assertRedirect();
    $admin->refresh();
    expect(Hash::check('newpassword888', $admin->password))->toBeTrue();
});

test('admin can export orders list to csv file with utf-8 bom', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->get(route('admin.orders.export'));
    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

test('admin can logout safely', function () {
    $admin = User::where('email', 'admin@gao.vn')->first();

    $response = $this->actingAs($admin)->post(route('admin.logout'));
    $response->assertRedirect(route('admin.login'));
    $this->assertGuest();
});
