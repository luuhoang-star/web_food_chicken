<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    /**
     * Tra cứu trạng thái đơn hàng theo Mã đơn hoặc Số điện thoại.
     */
    public function index(Request $request): View
    {
        $query = trim((string) ($request->input('q') ?? $request->input('code') ?? $request->input('phone') ?? ''));
        $hasSearched = $query !== '';
        $activeOrder = null;
        $recentOrders = collect();

        if ($hasSearched && Schema::hasTable('orders')) {
            // 1. Tìm chính xác theo mã đơn hàng
            $activeOrder = Order::with('items')
                ->where('order_code', $query)
                ->orWhere('order_code', strtoupper($query))
                ->first();

            // 2. Nếu không tìm thấy theo mã đơn, tìm theo số điện thoại
            if (! $activeOrder) {
                $phoneClean = preg_replace('/[^0-9]/', '', $query);
                if ($phoneClean !== '') {
                    $recentOrders = Order::with('items')
                        ->where('customer_phone', 'LIKE', "%{$phoneClean}%")
                        ->latest()
                        ->get();

                    $activeOrder = $recentOrders->first();
                }
            } else {
                // Nếu tìm thấy mã đơn, tìm thêm các đơn gần đây cùng số điện thoại
                $recentOrders = Order::with('items')
                    ->where('customer_phone', $activeOrder->customer_phone)
                    ->where('id', '!=', $activeOrder->id)
                    ->latest()
                    ->take(5)
                    ->get();
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
