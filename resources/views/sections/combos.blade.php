<!-- SECTION: ĂN COMBO, LỜI HƠN (DYNAMIC FROM DATABASE) -->
<section id="combos" class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <x-section-heading 
            title="ĂN COMBO, LỜI HƠN"
            subtitle="Tiết kiệm tới 55.000đ khi đi theo nhóm, ăn no nê cùng bạn bè & người thân."
            class="mb-14"
        />

        <!-- Dynamic Combo Cards from Database -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
            @forelse($combos ?? [] as $index => $combo)
                <x-combo-card :combo="$combo" :index="$index" />
            @empty
                <div class="col-span-3 text-center py-8 text-gray-500">
                    Đang cập nhật các gói Combo...
                </div>
            @endforelse
        </div>

    </div>
</section>
