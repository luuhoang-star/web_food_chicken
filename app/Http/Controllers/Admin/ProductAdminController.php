<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductAdminController extends Controller
{
    /**
     * Danh sách món ăn trong thực đơn với bộ lọc & sắp xếp nhanh.
     */
    public function index(Request $request): View
    {
        $categoryId = $request->input('category_id', 'all');
        $status = $request->input('status', 'all');
        $sort = $request->input('sort', 'latest');
        $perPage = in_array((int) $request->input('per_page'), [15, 30, 50]) ? (int) $request->input('per_page') : 15;
        $search = trim((string) $request->input('q', ''));

        $query = Product::with('category', 'sauce')
            ->withSum('orderItems as sold_count', 'quantity');

        // 1. Lọc theo danh mục
        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        // 2. Lọc theo trạng thái mở bán
        if ($status === 'available') {
            $query->where('is_available', true);
        } elseif ($status === 'out_of_stock') {
            $query->where('is_available', false);
        }

        // 3. Tìm kiếm theo tên / tag / slug
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('tag', 'LIKE', "%{$search}%");
            });
        }

        // 4. Sắp xếp
        if ($sort === 'best_seller') {
            $query->orderByRaw('COALESCE(sold_count, 0) DESC')->orderBy('id', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->orderBy('order', 'asc')->orderBy('id', 'desc');
        }

        return view('admin.products.index', [
            'products' => $query->paginate($perPage)->withQueryString(),
            'categories' => Category::ordered()->get(),
            'selectedCategory' => $categoryId,
            'selectedStatus' => $status,
            'selectedSort' => $sort,
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    /**
     * Giao diện form thêm món ăn mới.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::ordered()->get(),
            'sauces' => Sauce::ordered()->get(),
        ]);
    }

    /**
     * Lưu món ăn mới vào cơ sở dữ liệu.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        $imagePath = $this->handleImageUpload(
            $request->file('image_file'),
            $request->input('image_url') ?: $request->input('image'),
            'images/placeholder.jpg'
        );

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $this->generateUniqueSlug($validated['name']),
            'category_id' => $validated['category_id'],
            'sauce_id' => $request->filled('sauce_id') ? $request->input('sauce_id') : null,
            'sauce_selection' => $request->input('sauce_selection', 'none'),
            'price' => $validated['price'],
            'original_price' => $request->filled('original_price') ? $request->input('original_price') : null,
            'tag' => $request->filled('tag') ? $request->input('tag') : null,
            'subtag' => $request->filled('subtag') ? $request->input('subtag') : null,
            'description' => $request->filled('description') ? $request->input('description') : null,
            'image' => $imagePath,
            'is_hot' => (bool) $request->boolean('is_hot'),
            'is_available' => $request->has('is_available') ? (bool) $request->boolean('is_available') : true,
            'order' => $request->filled('order') ? (int) $request->input('order') : ((Product::max('order') ?? 0) + 1),
            'rating' => 5.0,
            'review_count' => 1,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã thêm món mới \"{$product->name}\" vào thực đơn thành công!");
    }

    /**
     * Giao diện form chỉnh sửa món ăn.
     */
    public function edit(int $id): View
    {
        return view('admin.products.edit', [
            'product' => Product::findOrFail($id),
            'categories' => Category::ordered()->get(),
            'sauces' => Sauce::ordered()->get(),
        ]);
    }

    /**
     * Cập nhật thông tin chi tiết món ăn.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $validated = $this->validateProduct($request);

        $imagePath = $this->handleImageUpload(
            $request->file('image_file'),
            $request->input('image_url') ?: $request->input('image'),
            $product->image
        );

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'sauce_id' => $request->has('sauce_id') ? ($request->input('sauce_id') ?: null) : $product->sauce_id,
            'sauce_selection' => $request->input('sauce_selection', $product->sauce_selection ?? 'none'),
            'price' => $validated['price'],
            'original_price' => $request->filled('original_price') ? $request->input('original_price') : null,
            'tag' => $request->filled('tag') ? $request->input('tag') : null,
            'subtag' => $request->filled('subtag') ? $request->input('subtag') : null,
            'description' => $request->filled('description') ? $request->input('description') : null,
            'image' => $imagePath,
            'is_hot' => (bool) $request->boolean('is_hot'),
            'is_available' => (bool) $request->boolean('is_available'),
            'order' => $request->filled('order') ? (int) $request->input('order') : $product->order,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', "Đã cập nhật thông tin món \"{$product->name}\" thành công!");
    }

    /**
     * Xoá món ăn khỏi thực đơn.
     */
    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Đã xoá món \"{$productName}\" khỏi thực đơn thành công!");
    }

    /**
     * Cập nhật nhanh giá tiền của món ăn.
     */
    public function updatePrice(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là chữ số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'price' => (float) $request->input('price'),
            'original_price' => $request->filled('original_price') ? (float) $request->input('original_price') : $product->original_price,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'price' => (float) $product->price,
                'formatted_price' => number_format((float) $product->price, 0, ',', '.').' ₫',
                'message' => "Đã lưu giá món \"{$product->name}\" thành công!",
            ]);
        }

        return back()->with('success', "Đã cập nhật giá món \"{$product->name}\" thành công!");
    }

    /**
     * Bật / Tắt trạng thái mở bán (Đang bán / Hết món).
     */
    public function toggleAvailability(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update([
            'is_available' => ! $product->is_available,
        ]);

        $statusText = $product->is_available ? 'Đang bán' : 'Hết món';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_available' => (bool) $product->is_available,
                'status_label' => $statusText,
                'message' => "Đã chuyển trạng thái món \"{$product->name}\" sang: {$statusText}!",
            ]);
        }

        return back()->with('success', "Đã chuyển trạng thái món \"{$product->name}\" sang: {$statusText}!");
    }

    /**
     * Thao tác hàng loạt (Bulk Actions).
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['exists:products,id'],
            'action' => ['required', 'string', 'in:available,out_of_stock,delete'],
        ]);

        $ids = $request->input('ids', []);
        $action = $request->input('action');
        $count = count($ids);

        if ($action === 'available') {
            Product::whereIn('id', $ids)->update(['is_available' => true]);
            $message = "Đã chuyển {$count} món được chọn sang trạng thái ĐANG BÁN!";
        } elseif ($action === 'out_of_stock') {
            Product::whereIn('id', $ids)->update(['is_available' => false]);
            $message = "Đã chuyển {$count} món được chọn sang trạng thái HẾT MÓN!";
        } elseif ($action === 'delete') {
            Product::whereIn('id', $ids)->delete();
            $message = "Đã xoá {$count} món được chọn khỏi thực đơn thành công!";
        }

        return back()->with('success', $message);
    }

    /**
     * Validate dữ liệu form món ăn.
     */
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'sauce_id' => ['nullable', 'exists:sauces,id'],
            'sauce_selection' => ['nullable', 'string', 'in:none,fixed,required'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'tag' => ['nullable', 'string', 'max:50'],
            'subtag' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:500'],
            'is_hot' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
            'order' => ['nullable', 'integer'],
        ], [
            'name.required' => 'Vui lòng nhập tên món ăn.',
            'category_id.required' => 'Vui lòng chọn danh mục món.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'price.required' => 'Vui lòng nhập giá bán.',
            'price.numeric' => 'Giá bán phải là số.',
            'price.min' => 'Giá bán không được âm.',
            'image_file.image' => 'File tải lên phải là hình ảnh (jpg, png, webp).',
            'image_file.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);
    }

    /**
     * Xử lý tải ảnh lên hoặc giữ URL cũ/fallback.
     */
    private function handleImageUpload(?UploadedFile $file, ?string $url, string $fallback): string
    {
        if ($file) {
            $uploadDir = public_path('images/products');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }
            $fileName = 'prod_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $fileName);

            return 'images/products/'.$fileName;
        }

        if (! empty($url)) {
            return trim($url);
        }

        return $fallback;
    }

    /**
     * Tạo slug duy nhất cho món ăn.
     */
    private function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-".Str::random(4);
            $counter++;
            if ($counter > 10) {
                $slug = "{$baseSlug}-".time();
                break;
            }
        }

        return $slug;
    }
}
