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
    protected static ?array $cachedData = null;

    /**
     * Bind global storefront data to all views efficiently.
     */
    public function compose(View $view): void
    {
        if (static::$cachedData === null) {
            static::$cachedData = [
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
                    ->orderBy('order')
                    ->get(),
            ];
        }

        $view->with(static::$cachedData);
    }
}
