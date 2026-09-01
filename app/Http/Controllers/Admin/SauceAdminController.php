<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sauce;
use App\Models\Topping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
     * Thêm vị sốt mới vào hệ thống.
     */
    public function storeSauce(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Vui lòng nhập tên vị sốt.',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Sauce::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-".Str::random(4);
            $counter++;
            if ($counter > 10) {
                $slug = "{$baseSlug}-".time();
                break;
            }
        }

        $colors = ['bg-red-600 ring-red-200', 'bg-amber-400 ring-amber-200', 'bg-lime-500 ring-lime-200', 'bg-orange-600 ring-orange-200', 'bg-purple-600 ring-purple-200', 'bg-pink-600 ring-pink-200'];
        $chosenColor = $colors[rand(0, count($colors) - 1)];

        $sauce = Sauce::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? '',
            'price' => $validated['price'] ?? 10000,
            'description' => $validated['description'] ?? null,
            'is_available' => true,
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã thêm vị sốt mới \"{$sauce->name}\" thành công!",
                'sauce' => [
                    'id' => $sauce->id,
                    'name' => $sauce->name,
                    'slug' => $sauce->slug,
                    'tagline' => $sauce->tagline,
                    'price' => (int) $sauce->price,
                    'description' => $sauce->description,
                    'color' => $chosenColor,
                    'saved_name' => $sauce->name,
                    'saved_price' => (int) $sauce->price,
                    'saved_tagline' => $sauce->tagline,
                    'is_dirty' => false,
                ],
            ]);
        }

        return back()->with('success', "Đã thêm vị sốt mới \"{$sauce->name}\" thành công!");
    }

    /**
     * Cập nhật thông tin và giá hũ sốt bán thêm.
     */
    public function updateSauce(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $sauce = Sauce::findOrFail($id);
        $updateData = [];
        if ($request->filled('name')) {
            $updateData['name'] = $request->input('name');
        }
        if ($request->has('tagline')) {
            $updateData['tagline'] = $request->input('tagline');
        }
        if ($request->filled('price')) {
            $updateData['price'] = $request->input('price');
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->input('description');
        }

        $sauce->update($updateData);

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
     * Xoá vị sốt khỏi hệ thống.
     */
    public function destroySauce(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $sauce = Sauce::findOrFail($id);
        $name = $sauce->name;
        $sauce->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xoá vị sốt \"{$name}\" thành công!",
            ]);
        }

        return back()->with('success', "Đã xoá vị sốt \"{$name}\" thành công!");
    }

    /**
     * Thêm topping mới vào hệ thống.
     */
    public function storeTopping(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Vui lòng nhập tên topping.',
        ]);

        $topping = Topping::create([
            'name' => $validated['name'],
            'price' => $validated['price'] ?? 0,
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã thêm topping \"{$topping->name}\" thành công!",
                'topping' => [
                    'id' => $topping->id,
                    'name' => $topping->name,
                    'price' => (int) $topping->price,
                    'is_available' => true,
                    'saved_name' => $topping->name,
                    'saved_price' => (int) $topping->price,
                    'is_dirty' => false,
                    'is_loading' => false,
                ],
            ]);
        }

        return back()->with('success', "Đã thêm topping \"{$topping->name}\" thành công!");
    }

    /**
     * Cập nhật giá và trạng thái topping.
     */
    public function updateTopping(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'price' => ['nullable', 'numeric', 'min:0'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $topping = Topping::findOrFail($id);
        $updateData = [];
        if ($request->filled('price')) {
            $updateData['price'] = $request->input('price');
        }
        if ($request->filled('name')) {
            $updateData['name'] = $request->input('name');
        }

        $topping->update($updateData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật topping \"{$topping->name}\" thành công!",
                'topping' => $topping,
                'formatted_price' => number_format((float) $topping->price, 0, ',', '.').' ₫',
            ]);
        }

        return back()->with('success', "Đã cập nhật topping \"{$topping->name}\" thành ".number_format((float) $topping->price, 0, ',', '.').' ₫!');
    }

    /**
     * Xoá topping khỏi hệ thống.
     */
    public function destroyTopping(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $topping = Topping::findOrFail($id);
        $name = $topping->name;
        $topping->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xoá topping \"{$name}\" thành công!",
            ]);
        }

        return back()->with('success', "Đã xoá topping \"{$name}\" thành công!");
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
