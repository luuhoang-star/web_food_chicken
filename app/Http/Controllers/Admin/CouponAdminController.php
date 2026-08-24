<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponAdminController extends Controller
{
    /**
     * Danh sách mã giảm giá.
     */
    public function index(): View
    {
        $coupons = Coupon::latest()->get();

        $activeCount = $coupons->filter(function ($c) {
            $isNotExpired = ! $c->expires_at || $c->expires_at->isFuture();
            $hasRemaining = ! $c->usage_limit || $c->used_count < $c->usage_limit;
            return $c->is_active && $isNotExpired && $hasRemaining;
        })->count();

        $totalUsedCount = $coupons->sum('used_count');

        $expiringCount = $coupons->filter(function ($c) {
            if (! $c->expires_at) return false;
            return $c->expires_at->isPast() || ($c->expires_at->diffInDays(Carbon::now()) <= 7 && $c->expires_at->isFuture());
        })->count();

        return view('admin.coupons.index', [
            'coupons' => $coupons,
            'activeCount' => $activeCount,
            'totalCount' => $coupons->count(),
            'totalUsedCount' => $totalUsedCount,
            'expiringCount' => $expiringCount,
        ]);
    }

    /**
     * Thêm mã giảm giá mới.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:fixed,percent'],
            'value' => ['required', 'numeric', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'name.required' => 'Vui lòng nhập tên chương trình ưu đãi.',
            'value.required' => 'Vui lòng nhập giá trị giảm.',
        ]);

        $code = strtoupper(str_replace(' ', '', trim((string) $request->input('code'))));

        $coupon = Coupon::create([
            'code' => $code,
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'min_order_amount' => $request->filled('min_order_amount') ? $request->input('min_order_amount') : 0,
            'max_discount' => $request->filled('max_discount') ? $request->input('max_discount') : null,
            'usage_limit' => $request->filled('usage_limit') ? $request->input('usage_limit') : null,
            'expires_at' => $request->filled('expires_at') ? $request->input('expires_at') : null,
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã tạo mã giảm giá \"{$coupon->code}\" thành công!",
                'coupon' => $coupon,
            ]);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', "Đã tạo mã giảm giá \"{$coupon->code}\" thành công!");
    }

    /**
     * Cập nhật thông tin mã giảm giá.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,' . $coupon->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:fixed,percent'],
            'value' => ['required', 'numeric', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $code = strtoupper(str_replace(' ', '', trim((string) $request->input('code'))));

        $coupon->update([
            'code' => $code,
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'value' => $request->input('value'),
            'min_order_amount' => $request->filled('min_order_amount') ? $request->input('min_order_amount') : 0,
            'max_discount' => $request->filled('max_discount') ? $request->input('max_discount') : null,
            'usage_limit' => $request->filled('usage_limit') ? $request->input('usage_limit') : null,
            'expires_at' => $request->filled('expires_at') ? $request->input('expires_at') : null,
            'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : $coupon->is_active,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật mã giảm giá \"{$coupon->code}\" thành công!",
                'coupon' => $coupon,
            ]);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', "Đã cập nhật mã giảm giá \"{$coupon->code}\" thành công!");
    }

    /**
     * Bật / Tắt trạng thái mã giảm giá.
     */
    public function toggle(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update([
            'is_active' => ! $coupon->is_active,
        ]);

        $statusText = $coupon->is_active ? 'Đang kích hoạt' : 'Tạm dừng áp dụng';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã chuyển mã \"{$coupon->code}\" sang: {$statusText}!",
                'is_active' => (bool) $coupon->is_active,
                'status_label' => $statusText,
            ]);
        }

        return back()->with('success', "Đã chuyển mã \"{$coupon->code}\" sang: {$statusText}!");
    }

    /**
     * Xoá mã giảm giá.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $code = $coupon->code;
        $coupon->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xoá mã giảm giá \"{$code}\" thành công!",
            ]);
        }

        return back()->with('success', "Đã xoá mã giảm giá \"{$code}\" thành công!");
    }
}
