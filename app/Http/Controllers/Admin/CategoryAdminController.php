<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryAdminController extends Controller
{
    /**
     * Danh sách các danh mục món ăn.
     */
    public function index(): View
    {
        $categories = Category::withCount('products')->ordered()->get();

        return view('admin.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Lưu danh mục món ăn mới.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
        ]);

        $baseSlug = Str::slug($request->input('name'));
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $category = Category::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'icon' => $request->input('icon') ?: '🍗',
            'order' => $request->filled('order') ? (int) $request->input('order') : ((Category::max('order') ?? 0) + 1),
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã thêm danh mục \"{$category->name}\" thành công!",
                'category' => $category,
            ]);
        }

        return back()->with('success', "Đã thêm danh mục \"{$category->name}\" thành công!");
    }

    /**
     * Cập nhật thông tin danh mục.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'order' => ['nullable', 'integer', 'min:1'],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
        ]);

        $category->update([
            'name' => $request->input('name'),
            'icon' => $request->input('icon') ?: $category->icon,
            'order' => $request->filled('order') ? (int) $request->input('order') : $category->order,
            'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : $category->is_active,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật danh mục \"{$category->name}\" thành công!",
                'category' => $category,
            ]);
        }

        return back()->with('success', "Đã cập nhật danh mục \"{$category->name}\" thành công!");
    }

    /**
     * Bật / Tắt trạng thái hiển thị của danh mục trên Menu.
     */
    public function toggle(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->update([
            'is_active' => ! $category->is_active,
        ]);

        $statusText = $category->is_active ? 'Đang hiển thị trên Menu' : 'Đã ẩn khỏi Menu';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã chuyển danh mục \"{$category->name}\" sang: {$statusText}!",
                'is_active' => (bool) $category->is_active,
                'status_label' => $statusText,
            ]);
        }

        return back()->with('success', "Đã chuyển danh mục \"{$category->name}\" sang: {$statusText}!");
    }

    /**
     * Xoá danh mục.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            $msg = "Không thể xoá danh mục \"{$category->name}\" vì đang có {$category->products_count} món ăn thuộc danh mục này. Hãy chuyển hoặc xoá các món trước!";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
            return back()->with('error', $msg);
        }

        $categoryName = $category->name;
        $category->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã xoá danh mục \"{$categoryName}\" thành công!",
            ]);
        }

        return back()->with('success', "Đã xoá danh mục \"{$categoryName}\" thành công!");
    }
}
