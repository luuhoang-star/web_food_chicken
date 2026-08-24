<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Bảng điều khiển Action-first Dashboard dành cho mô hình 1 người tự vận hành.
     */
    public function index(Request $request): View
    {
        $range = $request->input('range', 'today');
        $fromDateInput = $request->input('from_date');
        $toDateInput = $request->input('to_date');

        // Xác định khoảng thời gian lọc (Mặc định là 'today')
        $now = Carbon::now();
        $compareStartDate = null;
        $compareEndDate = null;
        $compareLabel = '';

        switch ($range) {
            case '7days':
                $startDate = $now->copy()->subDays(6)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $dateDisplay = $startDate->format('d/m/Y').' → '.$endDate->format('d/m/Y');
                $compareStartDate = $startDate->copy()->subDays(7);
                $compareEndDate = $startDate->copy()->subSecond();
                $compareLabel = 'kỳ trước';
                break;
            case '30days':
                $startDate = $now->copy()->subDays(29)->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $dateDisplay = $startDate->format('d/m/Y').' → '.$endDate->format('d/m/Y');
                $compareStartDate = $startDate->copy()->subDays(30);
                $compareEndDate = $startDate->copy()->subSecond();
                $compareLabel = 'kỳ trước';
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                $dateDisplay = $startDate->format('d/m/Y').' → '.$endDate->format('d/m/Y');
                $compareStartDate = $startDate->copy()->subMonth()->startOfMonth();
                $compareEndDate = $startDate->copy()->subMonth()->endOfMonth();
                $compareLabel = 'tháng trước';
                break;
            case 'custom':
                if ($fromDateInput && $toDateInput) {
                    $startDate = Carbon::parse($fromDateInput)->startOfDay();
                    $endDate = Carbon::parse($toDateInput)->endOfDay();
                    $dateDisplay = $startDate->format('d/m/Y').' → '.$endDate->format('d/m/Y');
                } else {
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $dateDisplay = $startDate->format('d/m/Y');
                    $range = 'today';
                }
                break;
            case 'today':
            default:
                $range = 'today';
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $dateDisplay = $startDate->format('d/m/Y');
                $compareStartDate = $now->copy()->subDay()->startOfDay();
                $compareEndDate = $now->copy()->subDay()->endOfDay();
                $compareLabel = 'hôm qua';
                break;
        }

        // Khởi tạo các chỉ số mặc định
        $periodRevenue = 0;
        $periodOrdersCount = 0;
        $completedOrdersCount = 0;
        $cancelledOrdersCount = 0;
        $pendingOrdersCount = 0;
        $avgOrderValue = 0;
        $revenueGrowthPercent = null;
        $actionableOrders = collect();
        $recentOrders = collect();
        $topProducts = collect();
        $todayStats = [
            'orders_count' => 0,
            'revenue' => 0,
            'completed_count' => 0,
            'cancelled_count' => 0,
            'dishes_count' => 0,
        ];
        $chartLabels = [];
        $chartRevenues = [];
        $chartOrders = [];

        if (Schema::hasTable('orders')) {
            // 1. Chỉ số tổng hợp trong kỳ lọc
            $basePeriodOrdersQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

            $periodRevenue = (float) $basePeriodOrdersQuery->clone()
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');

            $periodOrdersCount = $basePeriodOrdersQuery->clone()->count();

            $completedOrdersCount = $basePeriodOrdersQuery->clone()
                ->where('order_status', 'completed')
                ->count();

            $cancelledOrdersCount = $basePeriodOrdersQuery->clone()
                ->where('order_status', 'cancelled')
                ->count();

            $validOrdersCount = $periodOrdersCount - $cancelledOrdersCount;
            $avgOrderValue = $validOrdersCount > 0 ? (int) ($periodRevenue / $validOrdersCount) : 0;

            // So sánh % tăng trưởng doanh thu nếu có kỳ trước
            if ($compareStartDate && $compareEndDate) {
                $prevRevenue = (float) Order::whereBetween('created_at', [$compareStartDate, $compareEndDate])
                    ->where('order_status', '!=', 'cancelled')
                    ->sum('total_amount');
                if ($prevRevenue > 0) {
                    $revenueGrowthPercent = round((($periodRevenue - $prevRevenue) / $prevRevenue) * 100, 1);
                }
            }

            // 2. Đơn cần xử lý ngay (P0 Queue: pending -> confirmed -> preparing -> delivering)
            $actionableOrders = Order::with('items')
                ->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'processing', 'delivering', 'shipping'])
                ->orderBy('created_at', 'asc')
                ->get();

            $pendingOrdersCount = $actionableOrders->count();

            // 3. 5 Đơn gần nhất
            $recentOrders = Order::with('items')->latest()->take(5)->get();

            // 4. Thống kê "Tình hình hôm nay"
            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();
            $todayOrders = Order::whereBetween('created_at', [$todayStart, $todayEnd])->get();
            $todayStats = [
                'orders_count' => $todayOrders->count(),
                'revenue' => (float) $todayOrders->where('order_status', '!=', 'cancelled')->sum('total_amount'),
                'completed_count' => $todayOrders->where('order_status', 'completed')->count(),
                'cancelled_count' => $todayOrders->where('order_status', 'cancelled')->count(),
                'dishes_count' => 0,
            ];

            if (Schema::hasTable('order_items')) {
                $todayStats['dishes_count'] = (int) OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$todayStart, $todayEnd])
                    ->where('orders.order_status', '!=', 'cancelled')
                    ->sum('order_items.quantity');
            }

            // 5. Dữ liệu vẽ Biểu đồ theo từng ngày / theo giờ
            if ($range === 'today') {
                // Biểu đồ theo khung giờ trong ngày
                for ($h = 7; $h <= 22; $h += 2) {
                    $hStart = $startDate->copy()->setHour($h)->setMinute(0)->setSecond(0);
                    $hEnd = $startDate->copy()->setHour($h + 1)->setMinute(59)->setSecond(59);

                    $chartLabels[] = sprintf('%02dh', $h);
                    $hOrders = Order::whereBetween('created_at', [$hStart, $hEnd])->get();
                    $chartRevenues[] = (int) $hOrders->where('order_status', '!=', 'cancelled')->sum('total_amount');
                    $chartOrders[] = $hOrders->count();
                }
            } else {
                // Biểu đồ theo từng ngày
                $period = CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay());
                $dailyStats = Order::whereBetween('created_at', [$startDate, $endDate])
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(CASE WHEN order_status != "cancelled" THEN total_amount ELSE 0 END) as revenue'),
                        DB::raw('COUNT(*) as total_orders')
                    )
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->get()
                    ->keyBy('date');

                foreach ($period as $date) {
                    $dateKey = $date->format('Y-m-d');
                    $label = $date->format('d/m');
                    $chartLabels[] = $label;
                    $chartRevenues[] = isset($dailyStats[$dateKey]) ? (int) $dailyStats[$dateKey]->revenue : 0;
                    $chartOrders[] = isset($dailyStats[$dateKey]) ? (int) $dailyStats[$dateKey]->total_orders : 0;
                }
            }

            // 6. Top 5 Món bán chạy nhất trong kỳ (Số suất + Doanh thu)
            if (Schema::hasTable('order_items')) {
                $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereBetween('orders.created_at', [$startDate, $endDate])
                    ->where('orders.order_status', '!=', 'cancelled')
                    ->select(
                        'order_items.product_name',
                        'order_items.product_id',
                        DB::raw('SUM(order_items.quantity) as total_quantity'),
                        DB::raw('SUM(COALESCE(order_items.total_item_price, order_items.price * order_items.quantity)) as total_revenue')
                    )
                    ->groupBy('order_items.product_name', 'order_items.product_id')
                    ->orderByDesc('total_quantity')
                    ->take(5)
                    ->get();

                $productIds = $topProducts->pluck('product_id')->filter()->toArray();
                $productsMap = Product::whereIn('id', $productIds)->get()->keyBy('id');
                foreach ($topProducts as $item) {
                    $item->image_url = isset($productsMap[$item->product_id]) ? $productsMap[$item->product_id]->image_url : asset('images/placeholder.jpg');
                }
            }
        }

        return view('admin.dashboard', [
            'range' => $range,
            'dateDisplay' => $dateDisplay,
            'compareLabel' => $compareLabel,
            'revenueGrowthPercent' => $revenueGrowthPercent,
            'fromDate' => $startDate->format('Y-m-d'),
            'toDate' => $endDate->format('Y-m-d'),
            'periodRevenue' => $periodRevenue,
            'periodOrdersCount' => $periodOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'cancelledOrdersCount' => $cancelledOrdersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'avgOrderValue' => $avgOrderValue,
            'actionableOrders' => $actionableOrders,
            'recentOrders' => $recentOrders,
            'todayStats' => $todayStats,
            'topProducts' => $topProducts,
            'chartLabels' => $chartLabels,
            'chartRevenues' => $chartRevenues,
            'chartOrders' => $chartOrders,
        ]);
    }
}
