<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Illuminate\Contracts\View\View;

class SauceController extends Controller
{
    /**
     * Display the list of all active sauces for standalone purchase.
     * Route: /sot
     */
    public function index(): View
    {
        $sauces = Sauce::where('is_active', true)
            ->where('is_available', true)
            ->get();

        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $products = Product::where('is_available', true)->orderBy('order')->get();
        $spiceLevels = SpiceLevel::where('is_active', true)->orderBy('level')->get();
        $toppings = Topping::where('is_active', true)->get();
        $upsellItems = Product::whereHas('category', function ($query) {
            $query->whereIn('slug', ['drink', 'side']);
        })->where('is_available', true)->take(4)->get();

        return view('pages.sauces.index', compact(
            'sauces',
            'categories',
            'products',
            'spiceLevels',
            'toppings',
            'upsellItems'
        ));
    }

    /**
     * Display a specific sauce detail page for standalone purchase.
     * Route: /sot/{slug}
     */
    public function show(string $slug): View
    {
        $sauce = Sauce::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_available', true)
            ->firstOrFail();

        $sauces = Sauce::where('is_active', true)
            ->where('is_available', true)
            ->get();

        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $products = Product::where('is_available', true)->orderBy('order')->get();
        $spiceLevels = SpiceLevel::where('is_active', true)->orderBy('level')->get();
        $toppings = Topping::where('is_active', true)->get();
        $upsellItems = Product::whereHas('category', function ($query) {
            $query->whereIn('slug', ['drink', 'side']);
        })->where('is_available', true)->take(4)->get();

        return view('pages.sauces.show', compact(
            'sauce',
            'sauces',
            'categories',
            'products',
            'spiceLevels',
            'toppings',
            'upsellItems'
        ));
    }
}
