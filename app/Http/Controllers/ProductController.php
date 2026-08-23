<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
            ->available()
            ->firstOrFail();

        $relatedProducts = Product::with(['category', 'sauce', 'sauces'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->available()
            ->take(4)
            ->get();

        return view('pages.product', compact('product', 'relatedProducts'));
    }
}
