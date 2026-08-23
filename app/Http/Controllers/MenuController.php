<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\SpiceLevel;
use App\Models\Topping;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the official menu page.
     * Route: /menu (supports optional category filter ?category=rice or search query ?q=...)
     */
    public function index(Request $request): View
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true);
            }])
            ->orderBy('order')
            ->get();

        $selectedCategory = $request->query('category', 'all');
        $searchQuery = $request->query('q', '');

        $sauces = Sauce::where('is_active', true)
            ->where('is_available', true)
            ->get();

        $productsQuery = Product::with(['category', 'sauce', 'sauces'])
            ->where('is_available', true)
            ->orderBy('order');

        // Filter by Category if specified
        if ($selectedCategory !== 'all') {
            if ($selectedCategory === 'popular' || $selectedCategory === 'hot') {
                $productsQuery->where('is_hot', true);
            } else {
                $productsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                    $query->where('slug', $selectedCategory);
                });
            }
        }

        // Filter by Search Query
        if (! empty($searchQuery)) {
            $productsQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%");
            });
        }

        $products = $productsQuery->get();
        $allProducts = Product::with(['category', 'sauce', 'sauces'])->where('is_available', true)->orderBy('order')->get();

        $spiceLevels = SpiceLevel::where('is_active', true)->orderBy('level')->get();
        $toppings = Topping::where('is_active', true)->get();
        $upsellItems = Product::whereHas('category', function ($query) {
            $query->whereIn('slug', ['drink', 'side']);
        })->where('is_available', true)->take(4)->get();

        return view('pages.menu', compact(
            'categories',
            'products',
            'allProducts',
            'sauces',
            'spiceLevels',
            'toppings',
            'upsellItems',
            'selectedCategory',
            'searchQuery'
        ));
    }
}
