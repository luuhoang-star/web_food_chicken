<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Tra cứu trạng thái đơn hàng theo Mã đơn hoặc Số điện thoại chính xác.
     */
    public function index(Request $request): View
    {
        $query = trim((string) ($request->input('q') ?? $request->input('code') ?? $request->input('phone') ?? ''));
        $hasSearched = $query !== '';
        $activeOrder = null;
        $recentOrders = collect();

        if ($hasSearched && Schema::hasTable('orders')) {
            $upperCode = strtoupper($query);

            // 1. Tìm chính xác theo mã đơn hàng (VD: GAO-XXXXXX)
            $activeOrder = Order::with('items')
                ->where('order_code', $upperCode)
                ->orWhere('order_code', $query)
                ->first();

            // 2. Nếu không tìm thấy theo mã đơn, kiểm tra xem có phải số điện thoại hợp lệ (9 - 11 chữ số)
            if (! $activeOrder) {
                $phoneClean = preg_replace('/[^0-9]/', '', $query);
                // Bắt buộc SĐT phải có độ dài từ 9 đến 11 số để tránh việc quét mã ngắn (ví dụ: '09') làm lộ dữ liệu
                if (strlen($phoneClean) >= 9 && strlen($phoneClean) <= 11) {
                    $recentOrders = Order::with('items')
                        ->where('customer_phone', $phoneClean)
                        ->orWhere('customer_phone', $query)
                        ->latest()
                        ->get();

                    $activeOrder = $recentOrders->first();
                }
            } else {
                // Nếu tìm thấy theo mã đơn, lấy thêm các đơn gần đây cùng số điện thoại (tối đa 3 đơn)
                if (! empty($activeOrder->customer_phone)) {
                    $recentOrders = Order::with('items')
                        ->where('customer_phone', $activeOrder->customer_phone)
                        ->where('id', '!=', $activeOrder->id)
                        ->latest()
                        ->take(3)
                        ->get();
                }
            }
        }

        return view('pages.order-tracking', [
            'query' => $query,
            'hasSearched' => $hasSearched,
            'activeOrder' => $activeOrder,
            'recentOrders' => $recentOrders,
        ]);
    }
}
