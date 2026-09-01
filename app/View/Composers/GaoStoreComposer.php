<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SiteSetting;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class GaoStoreComposer
{
    /**
     * Cache global storefront data per request.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $cachedStoreData = null;

    /**
     * Cache admin data per request.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $cachedAdminData = null;

    /**
     * Bind global storefront data to views efficiently.
     */
    public function compose(View $view): void
    {
        $viewName = $view->getName();

        // Nếu là view Admin thì chỉ nạp cấu hình settings cần thiết, không query nặng menu/món ăn
        if (str_starts_with($viewName, 'admin.') || str_starts_with($viewName, 'layouts.admin')) {
            if (static::$cachedAdminData === null) {
                static::$cachedAdminData = [
                    'settings' => Schema::hasTable('site_settings') ? SiteSetting::allKeyed() : [],
                ];
            }
            $view->with(static::$cachedAdminData);

            return;
        }

        if (static::$cachedStoreData === null) {
            static::$cachedStoreData = [
                'settings' => Schema::hasTable('site_settings') ? SiteSetting::allKeyed() : [],
                'categories' => Category::active()
                    ->withCount(['products' => fn ($query) => $query->available()])
                    ->orderBy('order')
                    ->get(),
                'sauces' => Sauce::where('is_active', true)
                    ->where('is_available', true)
                    ->get(),
                'spiceLevels' => SpiceLevel::where('is_active', true)
                    ->orderBy('level')
                    ->get(),
                'toppings' => Topping::where('is_active', true)->get(),
                'upsellItems' => Product::with(['category', 'sauce', 'sauces'])
                    ->upsell()
                    ->available()
                    ->take(4)
                    ->get(),
                'allProducts' => Product::with(['category', 'sauce', 'sauces'])
                    ->available()
                    ->withSum(['orderItems as sold_count' => function ($q) {
                        $q->whereHas('order', fn ($o) => $o->where('order_status', '!=', 'cancelled'));
                    }], 'quantity')
                    ->orderBy('order')
                    ->get(),
            ];
        }

        $view->with(static::$cachedStoreData);
    }
}
