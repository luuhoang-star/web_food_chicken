<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Hero;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentAdminController extends Controller
{
    /**
     * Giao diện quản lý nội dung trang chủ.
     */
    public function index(): View
    {
        $hero = Hero::first();
        $benefits = Benefit::ordered()->get();
        $testimonials = Testimonial::ordered()->get();

        return view('admin.content.index', [
            'hero' => $hero,
            'benefits' => $benefits,
            'testimonials' => $testimonials,
        ]);
    }

    /**
     * Cập nhật Banner Hero đầu trang.
     */
    public function updateHero(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'badge' => ['required', 'string'],
            'title' => ['required', 'string'],
            'title_highlight' => ['required', 'string'],
            'description' => ['required', 'string'],
            'stat_number' => ['nullable', 'string'],
            'stat_label' => ['nullable', 'string'],
        ]);

        $hero = Hero::first() ?: new Hero;
        $hero->fill($request->only([
            'badge',
            'title',
            'title_highlight',
            'description',
            'stat_number',
            'stat_label',
        ]));
        $hero->is_active = true;
        $hero->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật Banner Hero thành công!',
                'hero' => $hero,
            ]);
        }

        return back()->with('success', 'Đã cập nhật nội dung Banner Hero thành công!');
    }

    /**
     * Cập nhật 1 cam kết chất lượng (Benefit).
     */
    public function updateBenefit(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'icon' => ['nullable', 'string'],
        ]);

        $benefit = Benefit::findOrFail($id);
        $updateData = $request->only(['title', 'description']);
        if ($request->filled('icon')) {
            $updateData['icon'] = $request->input('icon');
        }
        $benefit->update($updateData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật cam kết \"{$benefit->title}\" thành công!",
                'benefit' => $benefit,
            ]);
        }

        return back()->with('success', "Đã cập nhật cam kết \"{$benefit->title}\" thành công!");
    }

    /**
     * Thêm một đánh giá mới của khách hàng (Testimonial).
     */
    public function storeTestimonial(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'customer_name' => ['required', 'string'],
            'location' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'favorite_dish' => ['nullable', 'string'],
        ]);

        $content = $request->input('comment') ?: $request->input('content') ?: 'Gà rất giòn và ngon!';

        $testimonial = Testimonial::create([
            'customer_name' => $request->input('customer_name'),
            'location' => $request->input('location'),
            'rating' => (int) $request->input('rating'),
            'content' => $content,
            'favorite_dish' => $request->input('favorite_dish') ?: 'Cơm Gà Sốt Cay Hàn',
            'order' => (Testimonial::max('order') ?? 0) + 1,
            'is_active' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm đánh giá khách hàng mới thành công!',
                'testimonial' => $testimonial,
            ]);
        }

        return back()->with('success', 'Đã thêm đánh giá khách hàng mới thành công!');
    }

    /**
     * Cập nhật đánh giá của khách hàng.
     */
    public function updateTestimonial(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);

        $request->validate([
            'customer_name' => ['required', 'string'],
            'location' => ['required', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'favorite_dish' => ['nullable', 'string'],
        ]);

        $content = $request->input('comment') ?: $request->input('content') ?: $testimonial->content;

        $testimonial->update([
            'customer_name' => $request->input('customer_name'),
            'location' => $request->input('location'),
            'rating' => (int) $request->input('rating'),
            'content' => $content,
            'favorite_dish' => $request->input('favorite_dish') ?: $testimonial->favorite_dish,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật đánh giá khách hàng thành công!',
                'testimonial' => $testimonial,
            ]);
        }

        return back()->with('success', 'Đã cập nhật đánh giá khách hàng thành công!');
    }

    /**
     * Xoá đánh giá của khách hàng.
     */
    public function deleteTestimonial(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xoá đánh giá thành công!',
            ]);
        }

        return back()->with('success', 'Đã xoá đánh giá thành công!');
    }
}
