<?php

namespace App\Http\Controllers;

use App\Models\Benefit;
use App\Models\Combo;
use App\Models\Hero;
use App\Models\Product;
use App\Models\Sauce;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Display the main landing / discovery page.
     */
    public function index(): View
    {
        return view('pages.home', [
            'hero' => Schema::hasTable('heroes') ? Hero::active()->ordered()->first() : null,
            'featuredSauces' => Schema::hasTable('sauces') ? Sauce::active()->available()->orderBy('id')->limit(4)->get() : collect(),
            'popularProducts' => Schema::hasTable('products') ? Product::with(['category', 'sauce', 'sauces'])->available()->hot()->orderBy('order')->limit(8)->get() : collect(),
            'combos' => Schema::hasTable('combos') ? Combo::with('items.product')->active()->ordered()->limit(3)->get() : collect(),
            'benefits' => Schema::hasTable('benefits') ? Benefit::active()->ordered()->get() : collect(),
            'testimonials' => Schema::hasTable('testimonials') ? Testimonial::active()->ordered()->get() : collect(),
        ]);
    }

    /**
     * Display the Quality & Brand Commitments page.
     */
    public function quality(): View
    {
        return view('pages.quality', [
            'benefits' => Schema::hasTable('benefits') ? Benefit::active()->ordered()->get() : collect(),
            'sauces' => Schema::hasTable('sauces') ? Sauce::active()->available()->get() : collect(),
            'testimonials' => Schema::hasTable('testimonials') ? Testimonial::active()->ordered()->get() : collect(),
        ]);
    }
}
