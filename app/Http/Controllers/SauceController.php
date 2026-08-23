<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

/**
 * @deprecated All sauce viewing and filtering logic has been merged into MenuController.
 */
class SauceController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('menu');
    }

    public function show(string $slug): RedirectResponse
    {
        return redirect()->route('menu', ['sauce' => $slug]);
    }
}
