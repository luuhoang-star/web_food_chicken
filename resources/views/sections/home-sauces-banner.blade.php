<!-- HOME SAUCES TEASER / SPOTLIGHT BANNER -->
<section class="py-14 bg-white border-b border-orange-100/60 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <x-section-heading 
            badge="TINH HOA HƯƠNG VỊ"
            badgeIcon="🌶️"
            title="4 VỊ SỐT ĐẶC TRƯNG TẠI GAO"
            subtitle="Sốt thủ công nguyên bản, sánh mịn thơm lừng phủ đẫm trên từng miếng gà giòn rụm."
            align="left"
            :actionUrl="route('menu')"
            actionText="Xem thực đơn đặt món"
        />

        <!-- 4 Sauces Grid Cards (Clicking goes to /menu?sauce=slug) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($sauces ?? [] as $sauce)
                <x-sauce-card :sauce="$sauce" />
            @endforeach
        </div>

    </div>
</section>
