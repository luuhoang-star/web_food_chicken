<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
     * Display a single product's detail page with complete customization options.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['category', 'sauce', 'sauces'])
            ->where('slug', $slug)
            ->where('is_available', true)
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'sauce', 'sauces'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->take(4)
            ->get();

        $allProducts = Product::with(['category', 'sauce', 'sauces'])->where('is_available', true)->orderBy('order')->get();
        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $sauces = Sauce::where('is_active', true)->get();
        $spiceLevels = SpiceLevel::where('is_active', true)->orderBy('level')->get();
        $toppings = Topping::where('is_active', true)->get();
        $upsellItems = Product::with(['category', 'sauce', 'sauces'])
            ->whereHas('category', function ($query) {
                $query->whereIn('slug', ['drink', 'side']);
            })
            ->where('is_available', true)
            ->take(4)
            ->get();

        return view('pages.product', compact(
            'product',
            'relatedProducts',
            'allProducts',
            'categories',
            'sauces',
            'spiceLevels',
            'toppings',
            'upsellItems'
        ));
    }
}
