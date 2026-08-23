<!-- SECTION: BENEFIT BAR -->
<section id="benefits" class="py-10 bg-[#FAF6F0] scroll-mt-24">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/70 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-gray-100">
            @forelse($benefits ?? [] as $benefit)
                <x-benefit-card :benefit="$benefit" />
            @empty
                <div class="col-span-3 text-center text-gray-500 py-4">
                    Đang cập nhật cam kết chất lượng...
                </div>
            @endforelse
        </div>
    </div>
</section>
