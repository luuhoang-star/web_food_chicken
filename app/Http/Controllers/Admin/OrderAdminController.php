<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderAdminController extends Controller
{
    /**
     * Danh sách đơn hàng kèm bộ lọc trạng thái và thời gian.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');
        $dateFilter = $request->input('date', 'all');
        $search = trim((string) $request->input('q', ''));

        $orders = Order::with('items')
            ->filterAdmin($status, $dateFilter, $search)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Đếm số lượng đơn theo từng tab (gộp thành 1 câu query duy nhất tối ưu tốc độ)
        $countQuery = Order::query()->filterDate($dateFilter);

        $counts = (clone $countQuery)
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN order_status = 'confirmed' THEN 1 END) as confirmed,
                COUNT(CASE WHEN order_status IN ('preparing', 'processing') THEN 1 END) as preparing,
                COUNT(CASE WHEN order_status IN ('delivering', 'shipping') THEN 1 END) as delivering,
                COUNT(CASE WHEN order_status = 'completed' THEN 1 END) as completed,
                COUNT(CASE WHEN order_status = 'cancelled' THEN 1 END) as cancelled
            ")
            ->first();

        $statusCounts = [
            'all' => (int) ($counts->total ?? 0),
            'pending' => (int) ($counts->pending ?? 0),
            'confirmed' => (int) ($counts->confirmed ?? 0),
            'preparing' => (int) ($counts->preparing ?? 0),
            'delivering' => (int) ($counts->delivering ?? 0),
            'completed' => (int) ($counts->completed ?? 0),
            'cancelled' => (int) ($counts->cancelled ?? 0),
        ];

        // Lấy ID đơn hàng lớn nhất hiện tại để phục vụ check đơn mới
        $latestOrderId = Order::max('id') ?? 0;

        return view('admin.orders.index', [
            'orders' => $orders,
            'currentStatus' => $status,
            'currentDate' => $dateFilter,
            'search' => $search,
            'statusCounts' => $statusCounts,
            'latestOrderId' => $latestOrderId,
        ]);
    }

    /**
     * Xuất danh sách đơn hàng ra file Excel / CSV có định dạng UTF-8 BOM chuẩn tiếng Việt.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $status = $request->input('status', 'all');
        $dateFilter = $request->input('date', 'all');
        $search = trim((string) $request->input('q', ''));

        $query = Order::with('items')
            ->filterAdmin($status, $dateFilter, $search)
            ->latest();

        $fileName = 'Don_Hang_GAO_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Ghi UTF-8 BOM để Excel trên Windows hiển thị đúng font tiếng Việt có dấu
            fwrite($handle, "\xEF\xBB\xBF");

            // Header hàng cột
            fputcsv($handle, [
                'Mã Đơn Hàng',
                'Thời Gian Đặt',
                'Tên Khách Hàng',
                'Số Điện Thoại',
                'Địa Chỉ Giao',
                'Quận/Huyện',
                'Ghi Chú Tài Xế',
                'Chi Tiết Món Ăn',
                'Phương Thức Thanh Toán',
                'Trạng Thái Thanh Toán',
                'Tiền Món (Tạm tính)',
                'Phí Vận Chuyển',
                'Giảm Giá Voucher',
                'Tổng Tiền Đơn',
                'Trạng Thái Đơn Hàng',
            ]);

            $query->chunk(100, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $itemsSummary = $order->items->map(function ($i) {
                        $txt = $i->quantity.'x '.$i->product_name;
                        if ($i->sauce) {
                            $txt .= ' (Sốt '.$i->sauce.')';
                        }
                        if (! empty($i->toppings) && is_array($i->toppings)) {
                            $txt .= ' [+Topping: '.implode(', ', $i->toppings).']';
                        }

                        return $txt;
                    })->implode('; ');

                    fputcsv($handle, [
                        $order->order_code,
                        $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
                        $order->customer_name,
                        $order->customer_phone,
                        $order->address,
                        $order->district,
                        $order->driver_note ?? '',
                        $itemsSummary,
                        $order->payment_method_label,
                        $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Thu tiền COD',
                        (float) $order->subtotal,
                        (float) $order->shipping_fee,
                        (float) $order->discount,
                        (float) $order->total_amount,
                        $order->status_label,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Cập nhật trạng thái đơn hàng và trạng thái thanh toán.
     */
    public function updateStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'order_status' => ['required', 'string', 'in:pending,confirmed,preparing,delivering,completed,cancelled'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid'],
        ]);

        $order = Order::findOrFail($id);

        $updateData = [
            'order_status' => $request->input('order_status'),
        ];

        if ($request->filled('payment_status')) {
            $updateData['payment_status'] = $request->input('payment_status');
        }

        if ($request->input('order_status') === 'completed') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã chuyển đơn #{$order->order_code} sang: {$order->status_label}!",
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'order_status' => $order->order_status,
                'status_label' => $order->status_label,
                'status_color' => $order->status_color,
                'payment_status' => $order->payment_status,
                'is_paid' => ($order->payment_status === 'paid'),
            ]);
        }

        return back()->with('success', "Đã cập nhật trạng thái đơn hàng #{$order->order_code} thành: {$order->status_label}!");
    }

    /**
     * API kiểm tra đơn hàng mới thời gian thực phục vụ chuông báo âm thanh và badge.
     */
    public function checkNewOrders(Request $request): JsonResponse
    {
        $lastOrderId = (int) $request->input('last_order_id', 0);

        // Lấy đơn hàng mới nhất trên hệ thống
        $latestOrder = Order::with('items')->latest('id')->first();
        $currentMaxId = $latestOrder ? $latestOrder->id : 0;

        // Đếm tổng số đơn đang cần làm / chờ xử lý
        $pendingTotal = Order::whereIn('order_status', ['pending', 'confirmed', 'preparing', 'processing'])->count();

        // Kiểm tra xem có đơn mới phát sinh hay không
        $hasNew = false;
        $newOrders = [];

        if ($lastOrderId > 0 && $currentMaxId > $lastOrderId) {
            $newOrders = Order::with('items')
                ->where('id', '>', $lastOrderId)
                ->where('order_status', '!=', 'cancelled')
                ->latest('id')
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_code' => $order->order_code,
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'total_amount' => (float) $order->total_amount,
                        'formatted_total' => number_format((float) $order->total_amount, 0, ',', '.').'đ',
                        'items_summary' => $order->items->pluck('product_name')->implode(', '),
                        'created_at_human' => $order->created_at ? $order->created_at->diffForHumans() : 'Vừa xong',
                    ];
                });

            $hasNew = $newOrders->isNotEmpty();
        }

        return response()->json([
            'success' => true,
            'current_max_id' => $currentMaxId,
            'has_new' => $hasNew,
            'new_count' => count($newOrders),
            'new_orders' => $newOrders,
            'latest_order' => $hasNew ? $newOrders->first() : null,
            'pending_total' => $pendingTotal,
        ]);
    }
}
