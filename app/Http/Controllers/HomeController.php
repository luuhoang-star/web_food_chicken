<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Display the main landing / discovery page.
     */
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true);
            }])
            ->orderBy('order')
            ->get();

        $sauces = Sauce::where('is_active', true)->get();
        $spiceLevels = SpiceLevel::where('is_active', true)->orderBy('level')->get();
        $toppings = Topping::where('is_active', true)->get();

        $products = Product::with(['category', 'sauce', 'sauces'])
            ->where('is_available', true)
            ->orderBy('order')
            ->get();

        $popularDishes = Product::with(['category', 'sauce', 'sauces'])
            ->where('is_available', true)
            ->where('is_hot', true)
            ->orderBy('order')
            ->get();

        $combos = Product::with(['category', 'sauce', 'sauces'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'combo');
            })
            ->where('is_available', true)
            ->orderBy('order')
            ->take(3)
            ->get();

        $upsellItems = Product::with(['category', 'sauce', 'sauces'])
            ->whereHas('category', function ($query) {
                $query->whereIn('slug', ['drink', 'side']);
            })
            ->where('is_available', true)
            ->take(4)
            ->get();

        return view('pages.home', compact(
            'categories',
            'sauces',
            'spiceLevels',
            'toppings',
            'products',
            'popularDishes',
            'combos',
            'upsellItems'
        ));
    }
}
