<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        $selectedCategory = $request->query('category', 'all');
        $searchQuery = $request->query('q', '');

        $productsQuery = Product::with(['category', 'sauce', 'sauces'])
            ->available()
            ->orderBy('order');

        // Filter by Category if specified
        if ($selectedCategory !== 'all') {
            if (in_array($selectedCategory, ['popular', 'hot'])) {
                $productsQuery->hot();
            } else {
                $productsQuery->whereHas('category', fn ($query) => $query->where('slug', $selectedCategory));
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

        return view('pages.menu', compact(
            'products',
            'selectedCategory',
            'searchQuery'
        ));
    }
}
