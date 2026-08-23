<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure all seeders have run for testing
    $this->seed();
});

test('(a) base dishes have full sku variants across all sauces with no missing records', function () {
    $sauces = Sauce::all();
    expect($sauces)->toHaveCount(4);

    $riceCat = Category::where('slug', 'rice')->first();
    $chickenCat = Category::where('slug', 'chicken')->first();

    // Check Cơm Gà variants for each sauce
    foreach ($sauces as $sauce) {
        $riceVariant = Product::where('category_id', $riceCat->id)
            ->where('sauce_id', $sauce->id)
            ->first();

        expect($riceVariant)->not->toBeNull(
            "Missing Cơm Gà variant for sauce: {$sauce->name}"
        );
        expect($riceVariant->sauce_selection)->toBe('fixed');
        expect($riceVariant->sauces->contains($sauce->id))->toBeTrue();
    }

    // Check Gà Sốt (Phần Lớn) variants for each sauce (including Gà Sốt Chua Ngọt)
    foreach ($sauces as $sauce) {
        $chickenVariant = Product::where('category_id', $chickenCat->id)
            ->where('sauce_id', $sauce->id)
            ->first();

        expect($chickenVariant)->not->toBeNull(
            "Missing Gà Sốt variant for sauce: {$sauce->name}"
        );
        expect($chickenVariant->sauce_selection)->toBe('fixed');
        expect($chickenVariant->sauces->contains($sauce->id))->toBeTrue();
    }
});

test('(b) combo products have sauce_selection required and link to all sauces', function () {
    $combos = Product::where('sauce_selection', 'required')->get();
    expect($combos->count())->toBeGreaterThanOrEqual(3);

    $allSauceCount = Sauce::count();

    foreach ($combos as $combo) {
        expect($combo->requiresSauceChoice())->toBeTrue();
        expect($combo->sauce_id)->toBeNull();
        expect($combo->sauces)->toHaveCount($allSauceCount);
    }
});

test('(c) menu page renders cleanly with categories and no sauce filter bar', function () {
    $response = $this->get(route('menu'));
    $response->assertStatus(200);

    $response->assertSee('THỰC ĐƠN ĐẶT MÓN');
    $response->assertSee('Cơm Gà');
    $response->assertSee('Gà Sốt');
    $response->assertSee('Combo');
    $response->assertDontSee('Lọc theo vị sốt');
});
