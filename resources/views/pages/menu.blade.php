@extends('layouts.app')

@section('title', 'Thực Đơn Đặt Món | GAO - Gà Sốt & Cơm Hà Nội')

@section('content')
<div class="py-6 sm:py-8 bg-[#FAF6F0]" x-data="{
    activeCategory: '{{ $selectedCategory ?? "all" }}',
    searchQuery: '{{ $searchQuery ?? "" }}',
    
    categoryMeta: {
        'popular': { desc: 'Những món đặc trưng được khách hàng yêu thích và đặt nhiều nhất mỗi ngày', cols: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' },
        'rice': { desc: 'Suất cơm dẻo nóng sốt đẫm vị, ăn kèm dưa góp thanh mát', cols: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' },
        'chicken': { desc: 'Từng miếng gà phi lê chiên giòn rụm, đẫm sốt đặc trưng', cols: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' },
        'combo': { desc: 'Set ăn trọn gói siêu tiết kiệm dành cho cá nhân, cặp đôi & nhóm', cols: 'grid-cols-1 sm:grid-cols-3' },
        'side': { desc: 'Món gọi thêm giòn tan, nhâm nhi vui miệng', cols: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' },
        'drink': { desc: 'Nước ngọt ướp lạnh sảng khoái, giải khát tức thì', cols: 'grid-cols-1 sm:grid-cols-2 max-w-2xl' }
    },

    get activeCategories() {
        return this.categories.filter(c => c.id !== 'all' && c.id !== 'popular');
    },

    getItemsByCategory(catSlug) {
        if (catSlug === 'popular') {
            return this.popularItems;
        }
        return this.allMenuItems.filter(item => item.category === catSlug);
    },

    get isSearching() {
        return this.searchQuery.trim().length > 0;
    },

    get searchResults() {
        if (!this.isSearching) return [];
        const q = this.searchQuery.toLowerCase();
        return this.allMenuItems.filter(item => 
            item.name.toLowerCase().includes(q) || 
            (item.description && item.description.toLowerCase().includes(q)) ||
            (item.sauce && item.sauce.toLowerCase().includes(q))
        );
    },

    selectCategory(catId) {
        this.activeCategory = catId;
        this.searchQuery = '';
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Nav Bar -->
        <div class="space-y-4 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight uppercase">
                        THỰC ĐƠN ĐẶT MÓN
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 font-medium">
                        Khám phá thực đơn cơm gà sốt, gà chiên giòn & combo tiết kiệm.
                    </p>
                </div>

                <!-- Live Search Box -->
                <div class="w-full sm:w-72 shrink-0">
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="🔍  Tìm món ăn..."
                            class="w-full pl-4 pr-9 py-2.5 rounded-full border border-gray-200 bg-white text-xs font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 shadow-xs placeholder:text-gray-400"
                        >
                        <button 
                            x-show="searchQuery" 
                            @click="searchQuery = ''"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs font-bold cursor-pointer"
                        >✕</button>
                    </div>
                </div>
            </div>

            <!-- Category Tab Pill Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none border-b border-orange-200/60 pt-1">
                <template x-for="cat in categories" :key="cat.id">
                    <button 
                        @click="selectCategory(cat.id)"
                        type="button"
                        class="px-4 py-2 rounded-full text-xs font-black whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-xs"
                        :class="activeCategory === cat.id && !isSearching ? 'bg-red-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'"
                    >
                        <span x-text="cat.icon">✨</span>
                        <span x-text="cat.name">Tất Cả</span>
                        <span 
                            class="text-[10px] px-1.5 py-0.2 rounded-full"
                            :class="activeCategory === cat.id && !isSearching ? 'bg-red-700/80 text-white' : 'bg-gray-100 text-gray-500'"
                            x-text="cat.count"
                        >10</span>
                    </button>
                </template>
            </div>
        </div>

        <!-- VIEW 1: SEARCH RESULTS -->
        <div x-show="isSearching" class="space-y-8">
            <!-- Món Đích Thực Theo Vị Sốt Đang Tìm -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-orange-200/80">
                    <h2 class="text-base sm:text-lg font-black text-gray-900">
                        Món ăn theo vị "<span class="text-red-600" x-text="searchQuery"></span>" (<span x-text="searchResults.length"></span> món)
                    </h2>
                    <button 
                        @click="searchQuery = ''" 
                        class="text-xs font-black text-red-600 hover:underline cursor-pointer"
                    >
                        Xóa tìm kiếm (Xem tất cả)
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5" x-show="searchResults.length > 0">
                    <template x-for="dish in searchResults" :key="dish.id">
                        @include('partials.dish-card')
                    </template>
                </div>
            </div>

            <!-- GỢI Ý COMBO TIẾT KIỆM (Áp Dụng Được Cho Vị Sốt Này) -->
            <div class="space-y-4 pt-2 border-t border-orange-200/70" x-show="searchResults.length > 0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🍱</span>
                        <div>
                            <h3 class="text-base sm:text-lg font-black text-gray-900 flex items-center gap-2">
                                <span>Hoặc chọn COMBO Tiết Kiệm</span>
                                <span class="text-[11px] text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">Áp dụng được vị sốt này</span>
                            </h3>
                            <p class="text-xs text-gray-500 font-medium">Tiết kiệm tới 40% — Bạn có thể chọn sốt này khi thêm Combo vào giỏ.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                    <template x-for="dish in getItemsByCategory('combo')" :key="dish.id">
                        @include('partials.dish-card')
                    </template>
                </div>
            </div>

            <!-- Empty Search State -->
            <div x-show="searchResults.length === 0" class="text-center py-12 space-y-3 bg-white rounded-3xl p-6 border border-gray-200/80 shadow-xs">
                <div class="text-3xl">🍗</div>
                <h3 class="text-base font-black text-gray-900">Không tìm thấy món ăn phù hợp</h3>
                <p class="text-xs text-gray-500">Vui lòng thử tìm kiếm với từ khóa khác.</p>
                <button 
                    @click="searchQuery = ''; activeCategory = 'all';" 
                    class="px-4 py-2 rounded-full bg-red-600 text-white font-extrabold text-xs shadow-md hover:bg-red-700 transition-colors cursor-pointer"
                >
                    Xem tất cả món
                </button>
            </div>
        </div>

        <!-- VIEW 2: CATEGORIZED SECTIONS (Khoảng Cách Gọn Gàng, Lấp Đầy Hàng Hoàn Hảo) -->
        <div x-show="!isSearching" class="space-y-8 sm:space-y-10">
            
            <!-- SECTION 0: MÓN ĐƯỢC GỌI NHIỀU (Luôn hiển thị đầu tiên khi xem Tất Cả hoặc khi chọn tab Bán Chạy) -->
            <section 
                id="category-section-popular"
                x-show="(activeCategory === 'all' || activeCategory === 'popular') && popularItems.length > 0" 
                class="space-y-4"
            >
                <!-- Section Header -->
                <div class="flex items-center justify-between pb-2 border-b border-orange-200/80">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-red-500 to-amber-500 text-white shadow-xs flex items-center justify-center text-base sm:text-lg">🔥</span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base sm:text-xl font-black text-gray-900 tracking-tight">
                                    Món Được Gọi Nhiều
                                </h2>
                                <span class="text-[10px] font-black uppercase tracking-wider bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Top Bán Chạy</span>
                            </div>
                            <p class="text-[11px] text-gray-500 font-medium hidden sm:block" x-text="categoryMeta['popular'] ? categoryMeta['popular'].desc : ''"></p>
                        </div>
                    </div>
                    <span 
                        class="text-xs font-black text-red-600 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full shrink-0" 
                        x-text="popularItems.length + ' món'"
                    ></span>
                </div>

                <!-- Products Grid: 4 cột đều đẹp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                    <template x-for="dish in popularItems" :key="'popular-' + dish.id">
                        @include('partials.dish-card')
                    </template>
                </div>
            </section>

            <!-- CÁC DANH MỤC TIẾP THEO (Cơm Gà, Gà, Combo, Ăn Kèm, Đồ Uống) -->
            <template x-for="cat in activeCategories" :key="cat.id">
                <section 
                    :id="'category-section-' + cat.id"
                    x-show="activeCategory === 'all' || activeCategory === cat.id" 
                    class="space-y-4"
                >
                    <!-- Section Header -->
                    <div class="flex items-center justify-between pb-2 border-b border-orange-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-white shadow-xs border border-orange-100 flex items-center justify-center text-base sm:text-lg" x-text="cat.icon">🍚</span>
                            <div>
                                <h2 class="text-base sm:text-xl font-black text-gray-900 tracking-tight" x-text="cat.name">
                                    Cơm Gà
                                </h2>
                                <p class="text-[11px] text-gray-500 font-medium hidden sm:block" x-text="categoryMeta[cat.id] ? categoryMeta[cat.id].desc : ''"></p>
                            </div>
                        </div>
                        <span 
                            class="text-xs font-black text-red-600 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full shrink-0" 
                            x-text="getItemsByCategory(cat.id).length + ' món'"
                        >4 món</span>
                    </div>

                    <!-- Products Grid: Tự Động Co Giãn Đều Đẹp Theo Nhóm (4 cột / 3 cột / 2 cột) -->
                    <div 
                        class="grid gap-4 sm:gap-5"
                        :class="categoryMeta[cat.id] ? categoryMeta[cat.id].cols : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'"
                    >
                        <template x-for="dish in getItemsByCategory(cat.id)" :key="dish.id">
                            @include('partials.dish-card')
                        </template>
                    </div>
                </section>
            </template>
        </div>

    </div>
</div>
@endsection
