<!-- SECTION: KHÁCH ĂN NÓI GÌ? (TESTIMONIALS) -->
<section class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <x-section-heading 
            title="KHÁCH ĂN NÓI GÌ?"
            subtitle="Hơn 10.000+ bữa ăn ngon đã được giao đến tay khách hàng tại Hà Nội"
        />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($testimonials ?? [] as $review)
                <x-review-card :review="$review" />
            @empty
                <div class="col-span-3 text-center text-gray-500 py-8">
                    Đang cập nhật đánh giá từ khách hàng...
                </div>
            @endforelse
        </div>

    </div>
</section>
