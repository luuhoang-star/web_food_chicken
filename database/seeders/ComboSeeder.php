<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComboSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('combo_items')->truncate();
        DB::table('combos')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Fetch available base products for linking
        $riceProduct = Product::where('name', 'like', '%Cơm%')->first();
        $chickenProduct = Product::where('name', 'like', '%Gà%')->first();
        $popcornProduct = Product::where('name', 'like', '%Popcorn%')->first();
        $friesProduct = Product::where('name', 'like', '%Khoai%')->first();
        $drinkProduct = Product::where('name', 'like', '%Coca%')->orWhere('name', 'like', '%Nước%')->first();

        // 2. Define 3 Combos
        $combosData = [
            [
                'name' => 'Combo 1 Người',
                'slug' => 'combo-1-nguoi',
                'subtag' => '🍱 Dành cho 1 người',
                'description' => '1 Cơm gà sốt tuỳ chọn + 1 Nước ngọt có gas.',
                'price' => 69000,
                'original_price' => 79000,
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80',
                'tag' => 'TIẾT KIỆM',
                'rating' => 4.9,
                'review_count' => 150,
                'is_hot' => true,
                'is_active' => true,
                'order' => 1,
                'items' => [
                    ['item_name' => 'Cơm Gà Sốt (Tuỳ chọn vị)', 'product_id' => $riceProduct?->id, 'quantity' => 1, 'order' => 1],
                    ['item_name' => 'Nước ngọt có gas', 'product_id' => $drinkProduct?->id, 'quantity' => 1, 'order' => 2],
                ],
            ],
            [
                'name' => 'Combo 2 Người',
                'slug' => 'combo-2-nguoi',
                'subtag' => '🍱 Ăn no gà — 2 người',
                'description' => '2 Cơm gà sốt tuỳ chọn + 2 Nước ngọt + 1 Khoai tây lắc.',
                'price' => 99000,
                'original_price' => 149000,
                'image' => 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=700&q=80',
                'tag' => 'BEST SELLER',
                'rating' => 5.0,
                'review_count' => 280,
                'is_hot' => true,
                'is_active' => true,
                'order' => 2,
                'items' => [
                    ['item_name' => '2 Cơm Gà Sốt (Tuỳ chọn vị)', 'product_id' => $riceProduct?->id, 'quantity' => 2, 'order' => 1],
                    ['item_name' => '2 Nước ngọt có gas', 'product_id' => $drinkProduct?->id, 'quantity' => 2, 'order' => 2],
                    ['item_name' => '1 Khoai tây lắc phô mai', 'product_id' => $friesProduct?->id, 'quantity' => 1, 'order' => 3],
                ],
            ],
            [
                'name' => 'Combo Nhóm (3-4 Người)',
                'slug' => 'combo-nhom-3-4-nguoi',
                'subtag' => '🍱 Party ngập tràn — 3-4 người',
                'description' => '1 Mẹt gà sốt 8 miếng (tuỳ chọn vị) + 1 Gà popcorn + 1 Khoai chiên + 3 Nước ngọt.',
                'price' => 149000,
                'original_price' => 262000,
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=700&q=80',
                'tag' => 'TIẾT KIỆM',
                'rating' => 4.9,
                'review_count' => 190,
                'is_hot' => true,
                'is_active' => true,
                'order' => 3,
                'items' => [
                    ['item_name' => '1 Mẹt Gà Sốt 8 Miếng (Tuỳ chọn vị)', 'product_id' => $chickenProduct?->id, 'quantity' => 1, 'order' => 1],
                    ['item_name' => '1 Gà Popcorn lắc giòn', 'product_id' => $popcornProduct?->id, 'quantity' => 1, 'order' => 2],
                    ['item_name' => '1 Khoai tây chiên giòn', 'product_id' => $friesProduct?->id, 'quantity' => 1, 'order' => 3],
                    ['item_name' => '3 Nước ngọt có gas', 'product_id' => $drinkProduct?->id, 'quantity' => 3, 'order' => 4],
                ],
            ],
        ];

        foreach ($combosData as $data) {
            $items = $data['items'];
            unset($data['items']);

            $combo = Combo::create($data);

            foreach ($items as $item) {
                ComboItem::create([
                    'combo_id' => $combo->id,
                    'product_id' => $item['product_id'],
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'order' => $item['order'],
                ]);
            }
        }
    }
}
