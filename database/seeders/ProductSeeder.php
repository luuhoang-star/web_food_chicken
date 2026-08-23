<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sauce;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');
        $sauces = Sauce::all()->keyBy('slug');
        $allSauceIds = $sauces->pluck('id')->toArray();

        // 1. Base dish templates to multiply by sauces
        $baseSauceDishes = [
            [
                'base_name' => 'Cơm Gà',
                'category_slug' => 'rice',
                'price' => 49000,
                'subtag_template' => '🍚 Cơm dẻo + Miếng gà sốt %s',
                'images' => [
                    'sot-cay-han' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80',
                    'sot-mat-ong' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=700&q=80',
                    'sot-bo-toi' => 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=700&q=80',
                    'sot-chua-ngot' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=700&q=80',
                ],
                'descriptions' => [
                    'sot-cay-han' => 'Suất cơm gồm miếng gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa góp.',
                    'sot-mat-ong' => 'Suất cơm gồm gà giòn óng ả phủ sốt mật ong hoa rừng ngọt dịu, ăn cùng cơm nóng và dưa góp thanh mát.',
                    'sot-bo-toi' => 'Suất cơm gồm gà giòn thơm nức mùi bơ tỏi phi vàng rụm, béo bùi ngập tràn từng hạt cơm dẻo.',
                    'sot-chua-ngot' => 'Suất cơm gồm gà chiên sốt hoa quả tươi mát kích thích vị giác, chua ngọt bùng nổ ăn không hề ngấy.',
                ],
                'tags' => [
                    'sot-cay-han' => 'BEST SELLER',
                    'sot-mat-ong' => null,
                    'sot-bo-toi' => null,
                    'sot-chua-ngot' => null,
                ],
                'ratings' => [
                    'sot-cay-han' => 4.9,
                    'sot-mat-ong' => 4.8,
                    'sot-bo-toi' => 4.9,
                    'sot-chua-ngot' => 4.7,
                ],
            ],
            [
                'base_name' => 'Gà Sốt',
                'category_slug' => 'chicken',
                'price' => 45000,
                'subtag_template' => '🍗 5-6 miếng gà sốt %s',
                'images' => [
                    'sot-cay-han' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=700&q=80',
                    'sot-mat-ong' => 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=700&q=80',
                    'sot-bo-toi' => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?auto=format&fit=crop&w=700&q=80',
                    'sot-chua-ngot' => 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=700&q=80',
                ],
                'descriptions' => [
                    'sot-cay-han' => 'Từng miếng gà phi lê không xương chiên giòn rụm đẫm sốt cay đỏ óng ả, rắc mè rang thơm bùi chuẩn vị Hàn.',
                    'sot-mat-ong' => 'Từng miếng gà chiên giòn tẩm sốt mật ong thơm phức, lớp da bóng bẩy cuốn hút mọi lứa tuổi.',
                    'sot-bo-toi' => 'Từng miếng gà giòn thơm nức mũi bơ tỏi phi, béo bùi ngập tràn từng miếng cắn giòn tan.',
                    'sot-chua-ngot' => 'Từng miếng gà giòn tan quyện sốt chua ngọt bắt vị, thanh mát giải ngấy cho bữa ăn sảng khoái.',
                ],
                'tags' => [
                    'sot-cay-han' => null,
                    'sot-mat-ong' => null,
                    'sot-bo-toi' => null,
                    'sot-chua-ngot' => null,
                ],
                'ratings' => [
                    'sot-cay-han' => 4.9,
                    'sot-mat-ong' => 4.8,
                    'sot-bo-toi' => 4.8,
                    'sot-chua-ngot' => 4.7,
                ],
            ],
        ];

        // Clean slate safely with foreign key constraint handling
        Schema::disableForeignKeyConstraints();
        Product::truncate();
        DB::table('product_sauces')->truncate();
        Schema::enableForeignKeyConstraints();

        $orderIndex = 1;

        // 2. Generate Base Dishes × Sauces (Fixed sauce variants)
        foreach ($baseSauceDishes as $base) {
            $catId = $categories[$base['category_slug']]->id ?? 1;

            foreach ($sauces as $sauceSlug => $sauce) {
                $suffix = isset($base['name_suffix']) ? ' '.$base['name_suffix'] : '';
                $cleanSauceName = str_replace('Sốt ', '', $sauce->name);
                $name = str_starts_with($base['base_name'], 'Gà Sốt')
                    ? 'Gà Sốt '.$cleanSauceName.$suffix
                    : $base['base_name'].' Sốt '.$cleanSauceName.$suffix;
                $slug = Str::slug($name);

                $product = Product::create([
                    'category_id' => $catId,
                    'sauce_id' => $sauce->id,
                    'sauce_selection' => 'fixed',
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $base['descriptions'][$sauceSlug] ?? $sauce->description,
                    'price' => $base['price'],
                    'original_price' => null,
                    'image' => $base['images'][$sauceSlug] ?? $sauce->image,
                    'tag' => $base['tags'][$sauceSlug] ?? null,
                    'rating' => $base['ratings'][$sauceSlug] ?? 4.8,
                    'review_count' => rand(150, 400),
                    'subtag' => sprintf($base['subtag_template'], $sauce->name),
                    'default_sauce' => $sauce->name,
                    'is_hot' => in_array($sauceSlug, ['sot-cay-han', 'sot-mat-ong']),
                    'is_available' => true,
                    'order' => $orderIndex++,
                ]);

                // Link to N-N pivot
                $product->sauces()->sync([$sauce->id]);
            }
        }

        // 3. Combos (Sốt Tự Chọn Vị - sauce_selection = required)
        $combos = [
            [
                'name' => 'Combo 1 Người',
                'slug' => 'combo-1-nguoi',
                'sauce_id' => null,
                'default_sauce' => null,
                'description' => '1 Cơm gà sốt tuỳ chọn + 1 Nước ngọt có gas.',
                'price' => 69000,
                'original_price' => 79000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80',
                'tag' => 'TIẾT KIỆM',
                'rating' => 4.9,
                'review_count' => 150,
                'subtag' => '🍱 Dành cho 1 người',
            ],
            [
                'name' => 'Combo 2 Người',
                'slug' => 'combo-2-nguoi',
                'sauce_id' => null,
                'default_sauce' => null,
                'description' => '2 Cơm gà sốt tuỳ chọn + 2 Nước ngọt + 1 Khoai tây lắc.',
                'price' => 99000,
                'original_price' => 149000,
                'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=700&q=80',
                'tag' => 'BEST SELLER',
                'rating' => 5.0,
                'review_count' => 280,
                'subtag' => '🍱 Ăn no gà — 2 người',
            ],
            [
                'name' => 'Combo Nhóm (3-4 Người)',
                'slug' => 'combo-nhom-3-4-nguoi',
                'sauce_id' => null,
                'default_sauce' => null,
                'description' => '1 Mẹt gà sốt 8 miếng (tuỳ chọn vị) + 1 Gà popcorn + 1 Khoai chiên + 3 Nước ngọt.',
                'price' => 149000,
                'original_price' => 262000,
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=700&q=80',
                'tag' => 'TIẾT KIỆM',
                'rating' => 4.9,
                'review_count' => 190,
                'subtag' => '🍱 Party ngập tràn — 3-4 người',
            ],
        ];

        foreach ($combos as $comboData) {
            $combo = Product::create([
                'category_id' => $categories['combo']->id ?? 3,
                'sauce_id' => null,
                'sauce_selection' => 'required',
                'name' => $comboData['name'],
                'slug' => $comboData['slug'],
                'description' => $comboData['description'],
                'price' => $comboData['price'],
                'original_price' => $comboData['original_price'],
                'image' => $comboData['image'],
                'tag' => $comboData['tag'],
                'rating' => $comboData['rating'],
                'review_count' => $comboData['review_count'],
                'subtag' => $comboData['subtag'],
                'default_sauce' => null,
                'is_hot' => true,
                'is_available' => true,
                'order' => $orderIndex++,
            ]);

            // Combo connects with ALL sauces in pivot
            $combo->sauces()->sync($allSauceIds);
        }

        // 4. Sides (4 Món Ăn Kèm Chuẩn 1 Hàng 4 Cột)
        $sides = [
            [
                'name' => 'Gà Popcorn Lắc Giòn',
                'slug' => 'ga-popcorn-lac-gion',
                'description' => 'Từng viên gà popcorn chiên phồng giòn tan, lắc phô mai thơm lừng ăn cực cuốn.',
                'price' => 35000,
                'image' => 'https://images.unsplash.com/photo-1585109649139-366815a0d713?auto=format&fit=crop&w=700&q=80',
                'tag' => null,
                'rating' => 4.8,
                'subtag' => '🍿 Gà viên giòn rụm',
            ],
            [
                'name' => 'Gà Rán Giòn (1 Miếng)',
                'slug' => 'ga-ran-gion-1-mieng',
                'description' => '1 Miếng gà phi lê tẩm bột chiên vàng giòn rụm, vỏ mỏng thịt mọng nước.',
                'price' => 15000,
                'image' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=700&q=80',
                'tag' => null,
                'rating' => 4.9,
                'subtag' => '🍗 1 miếng gà giòn rụm',
            ],
            [
                'name' => 'Khoai Tây Chiên Giòn',
                'slug' => 'khoai-tay-chien-gion',
                'description' => 'Khoai tây cắt thanh chiên vàng giòn rụm, lắc muối tiêu thơm ngon.',
                'price' => 20000,
                'image' => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=700&q=80',
                'tag' => null,
                'rating' => 4.7,
                'subtag' => '🍟 Khoai tây chiên giòn',
            ],
            [
                'name' => 'Trứng Ốp La Lòng Đào',
                'slug' => 'trung-op-la-long-dao',
                'description' => 'Trứng gà tươi ốp la lòng đào béo ngậy, chảy tràn trên cơm nóng hấp dẫn.',
                'price' => 10000,
                'image' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=700&q=80',
                'tag' => null,
                'rating' => 4.9,
                'subtag' => '🍳 Topping cơm gà',
            ],
        ];

        foreach ($sides as $side) {
            Product::create([
                'category_id' => $categories['side']->id ?? 4,
                'sauce_id' => null,
                'sauce_selection' => 'none',
                'name' => $side['name'],
                'slug' => $side['slug'],
                'description' => $side['description'],
                'price' => $side['price'],
                'original_price' => null,
                'image' => $side['image'],
                'tag' => $side['tag'],
                'rating' => $side['rating'],
                'review_count' => rand(100, 300),
                'subtag' => $side['subtag'],
                'default_sauce' => null,
                'is_hot' => false,
                'is_available' => true,
                'order' => $orderIndex++,
            ]);
        }

        // 5. Drinks (2 Món Đồ Uống Cân Đối)
        $drinks = [
            [
                'name' => 'Coca Cola (Lon 320ml)',
                'slug' => 'coca-cola-lon-320ml',
                'description' => 'Coca Cola mát lạnh đã khát, uống kèm gà chiên giòn chuẩn vị.',
                'price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=700&q=80',
                'subtag' => '🥤 Nước ngọt có gas',
            ],
            [
                'name' => 'Pepsi (Lon 320ml)',
                'slug' => 'pepsi-lon-320ml',
                'description' => 'Pepsi ướp lạnh sảng khoái, giải ngấy tức thì sau từng miếng gà.',
                'price' => 12000,
                'image' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=700&q=80',
                'subtag' => '🥤 Nước ngọt ướp lạnh',
            ],
        ];

        foreach ($drinks as $drink) {
            Product::create([
                'category_id' => $categories['drink']->id ?? 5,
                'sauce_id' => null,
                'sauce_selection' => 'none',
                'name' => $drink['name'],
                'slug' => $drink['slug'],
                'description' => $drink['description'],
                'price' => $drink['price'],
                'original_price' => null,
                'image' => $drink['image'],
                'tag' => null,
                'rating' => 5.0,
                'review_count' => rand(200, 400),
                'subtag' => $drink['subtag'],
                'default_sauce' => null,
                'is_hot' => false,
                'is_available' => true,
                'order' => $orderIndex++,
            ]);
        }
    }
}
