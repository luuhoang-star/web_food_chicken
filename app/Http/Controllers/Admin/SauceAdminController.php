<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sauce;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SauceAdminController extends Controller
{
    /**
     * Giao diện quản lý vị sốt và topping.
     */
    public function index(): View
    {
        $sauces = Sauce::ordered()->get();
        $toppings = Topping::ordered()->get();

        return view('admin.sauces.index', [
            'sauces' => $sauces,
            'toppings' => $toppings,
        ]);
    }

    /**
     * Cập nhật thông tin và giá hũ sốt bán thêm.
     */
    public function updateSauce(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'tagline' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $sauce = Sauce::findOrFail($id);
        $sauce->update([
            'tagline' => $request->input('tagline'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật vị sốt \"{$sauce->name}\" thành công!",
                'sauce' => $sauce,
                'formatted_price' => number_format((float) $sauce->price, 0, ',', '.').' ₫',
            ]);
        }

        return back()->with('success', "Đã cập nhật vị sốt \"{$sauce->name}\" thành công!");
    }

    /**
     * Cập nhật giá và trạng thái topping.
     */
    public function updateTopping(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $topping = Topping::findOrFail($id);
        $updateData = [
            'price' => $request->input('price'),
        ];
        if ($request->filled('name')) {
            $updateData['name'] = $request->input('name');
        }

        $topping->update($updateData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật giá topping \"{$topping->name}\" thành ".number_format((float) $topping->price, 0, ',', '.').' ₫!',
                'topping' => $topping,
                'formatted_price' => number_format((float) $topping->price, 0, ',', '.').' ₫',
            ]);
        }

        return back()->with('success', "Đã cập nhật topping \"{$topping->name}\" thành ".number_format((float) $topping->price, 0, ',', '.').' ₫!');
    }

    /**
     * Bật / Tắt trạng thái topping (Còn / Hết).
     */
    public function toggleTopping(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $topping = Topping::findOrFail($id);
        $topping->update([
            'is_available' => ! $topping->is_available,
        ]);

        $statusText = $topping->is_available ? 'Đang phục vụ' : 'Hết hàng';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã chuyển topping \"{$topping->name}\" sang: {$statusText}!",
                'is_available' => (bool) $topping->is_available,
                'status_label' => $statusText,
            ]);
        }

        return back()->with('success', "Đã chuyển topping \"{$topping->name}\" sang: {$statusText}!");
    }
}
