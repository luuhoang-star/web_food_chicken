<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComboAdminController extends Controller
{
    /**
     * Danh sách các combo ưu đãi kèm bộ lọc & tìm kiếm nhanh.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'latest');

        $query = Combo::with('items.product');

        // 1. Tìm kiếm
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('subtag', 'LIKE', "%{$search}%")
                    ->orWhere('tag', 'LIKE', "%{$search}%");
            });
        }

        // 2. Lọc trạng thái
        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        // 3. Sắp xếp
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'best_seller') {
            $query->orderByRaw("CASE WHEN tag = 'BEST SELLER' THEN 1 ELSE 0 END DESC")->orderBy('review_count', 'desc');
        } else {
            $query->ordered();
        }

        $combos = $query->get();

        return view('admin.combos.index', [
            'combos' => $combos,
            'search' => $search,
            'selectedStatus' => $status,
            'selectedSort' => $sort,
        ]);
    }

    /**
     * Giao diện tạo combo mới.
     */
    public function create(): View
    {
        $products = Product::available()->ordered()->get();

        return view('admin.combos.create', [
            'products' => $products,
        ]);
    }

    /**
     * Lưu combo mới vào cơ sở dữ liệu.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'tag' => ['nullable', 'string', 'max:50'],
            'subtag' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'string', 'max:1000'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'order' => ['nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
        ], [
            'name.required' => 'Vui lòng nhập tên combo.',
            'price.required' => 'Vui lòng nhập giá bán combo.',
        ]);

        $imagePath = $request->input('image');

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('image_file')) {
            $uploadDir = public_path('images/combos');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('image_file');
            $fileName = 'combo_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $imagePath = 'images/combos/'.$fileName;
        }

        if (empty($imagePath)) {
            $imagePath = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80';
        }

        $baseSlug = Str::slug($request->input('name'));
        $slug = $baseSlug;
        $counter = 1;
        while (Combo::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        DB::transaction(function () use ($request, $slug, $imagePath) {
            $combo = Combo::create([
                'name' => $request->input('name'),
                'slug' => $slug,
                'subtag' => $request->input('subtag') ?: '🍱 Combo siêu tiết kiệm',
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'original_price' => $request->input('original_price'),
                'image' => $imagePath,
                'tag' => $request->input('tag') ?: 'TIẾT KIỆM',
                'rating' => 5.0,
                'review_count' => rand(80, 250),
                'is_hot' => $request->has('is_hot') ? (bool) $request->boolean('is_hot') : false,
                'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : true,
                'order' => $request->filled('order') ? (int) $request->input('order') : ((Combo::max('order') ?? 0) + 1),
            ]);

            if ($request->has('items') && is_array($request->input('items'))) {
                $orderIndex = 1;
                foreach ($request->input('items') as $item) {
                    if (! empty($item['item_name'])) {
                        ComboItem::create([
                            'combo_id' => $combo->id,
                            'product_id' => ! empty($item['product_id']) ? (int) $item['product_id'] : null,
                            'item_name' => $item['item_name'],
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'order' => $orderIndex++,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.combos.index')->with('success', "Đã tạo combo \"{$request->input('name')}\" thành công!");
    }

    /**
     * Giao diện chỉnh sửa combo.
     */
    public function edit(int $id): View
    {
        $combo = Combo::with('items')->findOrFail($id);
        $products = Product::available()->ordered()->get();

        return view('admin.combos.edit', [
            'combo' => $combo,
            'products' => $products,
        ]);
    }

    /**
     * Cập nhật thông tin combo.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $combo = Combo::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'tag' => ['nullable', 'string', 'max:50'],
            'subtag' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'string', 'max:1000'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'order' => ['nullable', 'integer', 'min:1'],
            'items' => ['nullable', 'array'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.product_id' => ['nullable', 'integer'],
        ]);

        $imagePath = $combo->image;

        if ($request->hasFile('image_file')) {
            $uploadDir = public_path('images/combos');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $file = $request->file('image_file');
            $fileName = 'combo_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);
            $imagePath = 'images/combos/'.$fileName;
        } elseif ($request->filled('image')) {
            $imagePath = $request->input('image');
        }

        DB::transaction(function () use ($combo, $request, $imagePath) {
            $combo->update([
                'name' => $request->input('name'),
                'subtag' => $request->input('subtag'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'original_price' => $request->input('original_price'),
                'image' => $imagePath,
                'tag' => $request->input('tag'),
                'is_hot' => $request->has('is_hot') ? (bool) $request->boolean('is_hot') : false,
                'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : $combo->is_active,
                'order' => $request->filled('order') ? (int) $request->input('order') : $combo->order,
            ]);

            // Sync items: xoá cũ tạo lại
            $combo->items()->delete();

            if ($request->has('items') && is_array($request->input('items'))) {
                $orderIndex = 1;
                foreach ($request->input('items') as $item) {
                    if (! empty($item['item_name'])) {
                        ComboItem::create([
                            'combo_id' => $combo->id,
                            'product_id' => ! empty($item['product_id']) ? (int) $item['product_id'] : null,
                            'item_name' => $item['item_name'],
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'order' => $orderIndex++,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.combos.index')->with('success', "Đã cập nhật combo \"{$combo->name}\" thành công!");
    }

    /**
     * Bật / Tắt trạng thái mở bán combo (Hỗ trợ cả JSON AJAX).
     */
    public function toggle(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $combo = Combo::findOrFail($id);
        $combo->update([
            'is_active' => ! $combo->is_active,
        ]);

        $statusText = $combo->is_active ? 'Mở bán' : 'Tạm ngưng';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => (bool) $combo->is_active,
                'status_label' => $statusText,
                'message' => "Đã chuyển combo \"{$combo->name}\" sang: {$statusText}!",
            ]);
        }

        return back()->with('success', "Đã chuyển combo \"{$combo->name}\" sang: {$statusText}!");
    }

    /**
     * Xoá combo.
     */
    public function destroy(int $id): RedirectResponse
    {
        $combo = Combo::findOrFail($id);
        $comboName = $combo->name;
        $combo->items()->delete();
        $combo->delete();

        return back()->with('success', "Đã xoá combo \"{$comboName}\" thành công!");
    }
}
