<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Benefit;
use App\Models\Category;
use App\Models\Hero;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $categories = Category::active()->ordered()->get();
        $settings = SiteSetting::allKeyed();

        return view('admin.content.index', [
            'hero' => $hero,
            'benefits' => $benefits,
            'testimonials' => $testimonials,
            'categories' => $categories,
            'settings' => $settings,
        ]);
    }

    /**
     * Cập nhật Banner Hero đầu trang.
     */
    public function updateHero(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'badge' => ['nullable', 'string'],
            'title' => ['required', 'string'],
            'title_highlight' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'subtitle' => ['nullable', 'string'],
            'cta_primary_text' => ['nullable', 'string'],
            'cta_primary_url' => ['nullable', 'string'],
            'cta_secondary_text' => ['nullable', 'string'],
            'cta_secondary_url' => ['nullable', 'string'],
            'delivery_time' => ['nullable', 'string'],
            'hot_status' => ['nullable', 'string'],
            'rating' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'floating_badge' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'hero_image_file' => ['nullable', 'image', 'max:5120'],
            'stat_number' => ['nullable', 'string'],
            'stat_label' => ['nullable', 'string'],
        ]);

        $hero = Hero::first() ?: new Hero;

        $fillableData = $request->only([
            'badge',
            'title',
            'title_highlight',
            'cta_primary_text',
            'cta_primary_url',
            'cta_secondary_text',
            'cta_secondary_url',
            'delivery_time',
            'hot_status',
            'rating',
            'price',
            'floating_badge',
            'image',
            'stat_number',
            'stat_label',
        ]);

        if ($request->hasFile('hero_image_file')) {
            $file = $request->file('hero_image_file');
            $uploadDir = public_path('images/heroes');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $fileName = 'hero_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $fillableData['image'] = 'images/heroes/'.$fileName;
        }

        $hero->fill($fillableData);

        if ($request->filled('description')) {
            $hero->subtitle = $request->input('description');
        } elseif ($request->filled('subtitle')) {
            $hero->subtitle = $request->input('subtitle');
        }

        $hero->is_active = true;
        $hero->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật Banner Hero thành công!',
                'hero' => $hero,
            ]);
        }

        $redirectTo = $request->input('_redirect_to', route('admin.content.index', ['tab' => 'hero']));

        return redirect($redirectTo)->with('success', 'Đã cập nhật nội dung Banner Hero thành công!');
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
