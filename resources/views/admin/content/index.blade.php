@extends('layouts.admin')

@section('title', 'Quản Lý Giao Diện & Nội Dung Trang Chủ')
@section('page_title', '🎨 Quản Lý Giao Diện & Nội Dung Trang Chủ')

@section('content')
<div 
    class="max-w-5xl space-y-6 pb-24" 
    x-data="{
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'hero',
        toastMessage: '',
        isDirtyHero: false,
        showReviewDrawer: false,
        isEditingReview: false,
        activeMenuReviewId: null,

        setTab(tab) {
            this.activeTab = tab;
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        },

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        // 1. Header State
        headerState: {
            topNotification: '{{ addslashes($settings['top_notification'] ?? 'Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!') }}',
            hotline: '{{ addslashes($settings['hotline'] ?? '0988.868.GAO') }}',
            siteName: '{{ addslashes($settings['site_name'] ?? 'GAO') }}',
            siteTagline: '{{ addslashes($settings['site_tagline'] ?? 'GÀ SỐT & CƠM') }}',
            locationShort: '{{ addslashes($settings['location_short'] ?? 'Hà Nội') }}',
            locationBadge: '{{ addslashes($settings['location_badge'] ?? 'Hà Nội (3–5km)') }}',
            ctaText: '{{ addslashes($settings['header_cta_text'] ?? 'Đặt món') }}'
        },

        // 2. Hero State
        hero: {
            badge: '{{ addslashes($hero->badge ?? '🔥 GÀ CHIÊN + SỐT ĐẬM VỊ!') }}',
            title: '{{ addslashes($hero->title ?? 'GÀ GIÒN.') }}',
            title_highlight: '{{ addslashes($hero->title_highlight ?? 'SỐT ĐẬM.') }}',
            stat_number: '{{ addslashes($hero->stat_number ?? '15.000+') }}',
            stat_label: '{{ addslashes($hero->stat_label ?? 'suất cơm gà phục vụ mỗi tháng') }}',
            description: '{{ addslashes($hero->subtitle ?? $hero->description ?? 'Chuyên các món gà rán giòn rụm kết hợp cùng 4 loại sốt độc quyền chuẩn vị Hà Nội. Giao hàng nóng hổi tận tay trong 25–40 phút.') }}',
            saved: {}
        },

        // 3. Popup State
        popupState: {
            enabled: '{{ $settings['popup_enabled'] ?? '0' }}',
            title: '{{ addslashes($settings['popup_title'] ?? '🎉 Ưu Đãi Đặc Biệt Hôm Nay!') }}',
            description: '{{ addslashes($settings['popup_description'] ?? 'Tặng ngay 01 hũ sốt đặc trưng hoặc Freeship 3km cho đơn hàng từ 100k hôm nay. Đặt ngay để nhận ưu đãi!') }}',
            ctaText: '{{ addslashes($settings['popup_cta_text'] ?? 'Xem Thực Đơn Đặt Ngay →') }}',
            ctaUrl: '{{ addslashes($settings['popup_cta_url'] ?? '/#menu-section') }}',
            imageUrl: '{{ !empty($settings['popup_banner_image']) ? (str_starts_with($settings['popup_banner_image'], 'http') ? $settings['popup_banner_image'] : asset($settings['popup_banner_image'])) : '' }}',
            filePreview: null
        },

        // 4. Benefits State (3 cam kết)
        benefits: {{ json_encode($benefits->map(fn($b) => [
            'id' => $b->id,
            'icon' => $b->icon ?: '⚡',
            'title' => $b->title,
            'description' => $b->description,
            'saved_title' => $b->title,
            'saved_description' => $b->description,
            'saved_icon' => $b->icon ?: '⚡'
        ])) }},

        // 5. Testimonials State (Đánh giá)
        testimonials: {{ json_encode($testimonials->map(fn($t) => [
            'id' => $t->id,
            'customer_name' => $t->customer_name,
            'location' => $t->location,
            'favorite_dish' => $t->favorite_dish ?: 'Cơm Gà Sốt Cay Hàn',
            'rating' => (int)$t->rating,
            'content' => $t->comment ?: $t->content,
        ])) }},

        reviewForm: {
            id: null,
            customer_name: '',
            location: '',
            favorite_dish: 'Cơm Gà Sốt Cay Hàn',
            rating: 5,
            content: ''
        },

        init() {
            this.hero.saved = { ...this.hero };
        },

        checkHeroDirty() {
            this.isDirtyHero = this.hero.badge !== this.hero.saved.badge ||
                              this.hero.title !== this.hero.saved.title ||
                              this.hero.title_highlight !== this.hero.saved.title_highlight ||
                              this.hero.stat_number !== this.hero.saved.stat_number ||
                              this.hero.stat_label !== this.hero.saved.stat_label ||
                              this.hero.description !== this.hero.saved.description;
        },

        async saveHero() {
            try {
                const res = await fetch('{{ route('admin.content.hero.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        badge: this.hero.badge,
                        title: this.hero.title,
                        title_highlight: this.hero.title_highlight,
                        stat_number: this.hero.stat_number,
                        stat_label: this.hero.stat_label,
                        description: this.hero.description
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.hero.saved = { ...this.hero };
                    this.isDirtyHero = false;
                    this.showToast('Đã lưu Banner Hero thành công!');
                }
            } catch (e) {
                alert('Có lỗi khi lưu Banner Hero.');
            }
        },

        async saveBenefit(b) {
            try {
                const res = await fetch(`/admin/content/benefit/${b.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        title: b.title,
                        description: b.description,
                        icon: b.icon
                    })
                });
                const data = await res.json();
                if (data.success) {
                    b.saved_title = b.title;
                    b.saved_description = b.description;
                    b.saved_icon = b.icon;
                    this.showToast(`Đã lưu cam kết '${b.title}'!`);
                }
            } catch (e) {
                alert('Không thể lưu cam kết.');
            }
        },

        handlePopupFile(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.popupState.filePreview = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        openCreateReviewModal() {
            this.isEditingReview = false;
            this.reviewForm = {
                id: null,
                customer_name: '',
                location: '',
                favorite_dish: 'Cơm Gà Sốt Cay Hàn',
                rating: 5,
                content: ''
            };
            this.showReviewDrawer = true;
        },

        openEditReviewModal(r) {
            this.isEditingReview = true;
            this.reviewForm = {
                id: r.id,
                customer_name: r.customer_name,
                location: r.location,
                favorite_dish: r.favorite_dish,
                rating: r.rating,
                content: r.content
            };
            this.activeMenuReviewId = null;
            this.showReviewDrawer = true;
        },

        async saveReview() {
            if (!this.reviewForm.customer_name.trim() || !this.reviewForm.content.trim()) {
                alert('Vui lòng nhập tên khách hàng và nội dung đánh giá.');
                return;
            }

            try {
                if (this.isEditingReview) {
                    const res = await fetch(`/admin/content/testimonial/${this.reviewForm.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.reviewForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        const idx = this.testimonials.findIndex(t => t.id === this.reviewForm.id);
                        if (idx !== -1) {
                            this.testimonials[idx] = { ...this.reviewForm };
                        }
                        this.showReviewDrawer = false;
                        this.showToast('Đã cập nhật đánh giá thành công!');
                    }
                } else {
                    const res = await fetch('{{ route('admin.content.testimonial.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.reviewForm)
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.testimonials.push({
                            id: data.testimonial.id,
                            customer_name: data.testimonial.customer_name,
                            location: data.testimonial.location,
                            favorite_dish: data.testimonial.favorite_dish,
                            rating: data.testimonial.rating,
                            content: data.testimonial.content
                        });
                        this.showReviewDrawer = false;
                        this.showToast('Đã thêm đánh giá mới thành công!');
                    }
                }
            } catch (e) {
                alert('Không thể lưu đánh giá.');
            }
        },

        async deleteReview(r) {
            this.activeMenuReviewId = null;
            if (!confirm(`Xoá đánh giá của '${r.customer_name}'?`)) return;

            try {
                const res = await fetch(`/admin/content/testimonial/${r.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.testimonials = this.testimonials.filter(t => t.id !== r.id);
                    this.showToast('Đã xoá đánh giá!');
                }
            } catch (e) {
                alert('Không thể xoá đánh giá.');
            }
        }
    }"
    @click="activeMenuReviewId = null"
>

    <!-- FLOATING TOAST NOTIFICATION -->
    <div 
        x-show="toastMessage" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-4 py-2.5 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-2.5 text-xs font-bold"
        x-cloak
    >
        <span class="text-sm">🎨</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- 1. HEADER (TITLE + XEM TRANG CHỦ) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-1">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>🎨 Giao Diện & Nội Dung Trang Chủ</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Tất cả những gì khách hàng nhìn thấy trên website (xếp theo thứ tự từ trên xuống dưới).
            </p>
        </div>

        <a 
            href="/" 
            target="_blank" 
            class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors flex items-center gap-1.5 self-start sm:self-center"
        >
            <span>🌐 Xem Website Trực Tiếp</span>
            <span>↗</span>
        </a>
    </div>

    <!-- 2. TAB NAVIGATION: THỨ TỰ TỪ TRÊN XUỐNG DƯỚI CỦA TRANG WEB -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-200/80 shadow-xs flex items-center gap-1 overflow-x-auto text-xs font-bold scrollbar-thin">
        
        <button 
            type="button" 
            @click="setTab('header')" 
            :class="activeTab === 'header' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🔝 1. Đầu Trang (Header)</span>
        </button>

        <button 
            type="button" 
            @click="setTab('hero')" 
            :class="activeTab === 'hero' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🌟 2. Banner Hero Chính</span>
        </button>

        <button 
            type="button" 
            @click="setTab('popup')" 
            :class="activeTab === 'popup' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🎁 3. Popup Khuyến Mãi</span>
        </button>

        <button 
            type="button" 
            @click="setTab('benefits')" 
            :class="activeTab === 'benefits' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🛡️ 4. 3 Cam Kết Chất Lượng</span>
        </button>

        <button 
            type="button" 
            @click="setTab('testimonials')" 
            :class="activeTab === 'testimonials' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>💬 5. Đánh Giá Khách Hàng</span>
        </button>

        <button 
            type="button" 
            @click="setTab('footer')" 
            :class="activeTab === 'footer' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
            class="px-3.5 py-2.5 rounded-xl whitespace-nowrap transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
        >
            <span>🔻 6. Chân Trang (Footer)</span>
        </button>

    </div>

    <!-- TAB 1: 🔝 HEADER (ĐẦU TRANG & THANH THÔNG BÁO) -->
    <div x-show="activeTab === 'header'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'header']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🔝</span>
                            <span>Cấu Hình Header & Thanh Thông Báo Đầu Trang</span>
                        </h3>
                        <p class="text-xs text-gray-400">Thông báo khuyến mãi chạy trên cùng, số hotline và nút kêu gọi đặt món</p>
                    </div>
                </div>

                <!-- Live Preview Topbar -->
                <div class="p-3.5 bg-gray-900 text-white rounded-2xl space-y-2 text-xs">
                    <span class="text-[10px] font-black uppercase text-gray-400 block tracking-wider">👁️ Xem trước Thanh Thông Báo (Top Bar):</span>
                    <div class="flex items-center justify-between px-3 py-2 bg-gray-800 rounded-xl text-[11px] font-semibold flex-wrap gap-2">
                        <span class="text-amber-400" x-text="headerState.topNotification"></span>
                        <span class="text-gray-300 font-mono" x-text="'📞 Hotline: ' + headerState.hotline"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1 md:col-span-2">
                        <label class="block font-bold text-gray-700">Dòng chữ thông báo khuyến mãi đầu trang (Top Notification)</label>
                        <input 
                            type="text" 
                            name="top_notification" 
                            x-model="headerState.topNotification"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Số điện thoại Hotline</label>
                        <input 
                            type="text" 
                            name="hotline" 
                            x-model="headerState.hotline"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Tên thương hiệu Header (Logo Text)</label>
                        <input 
                            type="text" 
                            name="site_name" 
                            x-model="headerState.siteName"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khẩu hiệu phụ dưới Logo</label>
                        <input 
                            type="text" 
                            name="site_tagline" 
                            x-model="headerState.siteTagline"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Khu vực phục vụ (Badge hiển thị)</label>
                        <input 
                            type="text" 
                            name="location_badge" 
                            x-model="headerState.locationBadge"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cài Đặt Header
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 2: 🌟 BANNER HERO CHÍNH -->
    <div x-show="activeTab === 'hero'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🌟</span>
                        <span>Banner Hero Chính Đầu Trang</span>
                    </h3>
                    <p class="text-xs text-gray-400">Khẩu hiệu lớn, hình ảnh & số liệu nhận diện thương hiệu</p>
                </div>
                <button 
                    type="button" 
                    @click="saveHero()"
                    class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                >
                    💾 Lưu Banner Hero
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                <!-- Preview Hero Box -->
                <div class="lg:col-span-5 bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white p-5 rounded-2xl shadow-md space-y-3">
                    <span class="text-[9px] uppercase font-black tracking-widest text-gray-400 block">👁️ Xem trước Banner:</span>
                    
                    <div class="inline-block px-2.5 py-1 rounded-full bg-red-600/90 text-[10px] font-black text-white" x-text="hero.badge"></div>
                    
                    <div class="space-y-0.5">
                        <h2 class="text-2xl font-black tracking-tight uppercase leading-tight" x-text="hero.title"></h2>
                        <h2 class="text-2xl font-black tracking-tight text-red-500 uppercase leading-tight" x-text="hero.title_highlight"></h2>
                    </div>

                    <p class="text-xs text-gray-300 leading-relaxed font-medium line-clamp-3" x-text="hero.description"></p>

                    <div class="pt-2 border-t border-gray-700/60 flex items-center gap-2 text-xs">
                        <span class="font-black text-amber-400 font-mono text-sm" x-text="hero.stat_number"></span>
                        <span class="text-gray-400 text-[11px]" x-text="hero.stat_label"></span>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="lg:col-span-7 space-y-3 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Huy hiệu nhỏ (Badge nổi bật)</label>
                        <input 
                            type="text" 
                            x-model="hero.badge" 
                            @input="checkHeroDirty()"
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tiêu đề chính (Dòng 1)</label>
                            <input 
                                type="text" 
                                x-model="hero.title" 
                                @input="checkHeroDirty()"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tiêu đề nổi bật (Dòng 2)</label>
                            <input 
                                type="text" 
                                x-model="hero.title_highlight" 
                                @input="checkHeroDirty()"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-black text-red-600 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Số liệu ấn tượng</label>
                            <input 
                                type="text" 
                                x-model="hero.stat_number" 
                                @input="checkHeroDirty()"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                            >
                        </div>

                        <div class="sm:col-span-2 space-y-1">
                            <label class="block font-bold text-gray-700">Mô tả số liệu</label>
                            <input 
                                type="text" 
                                x-model="hero.stat_label" 
                                @input="checkHeroDirty()"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Đoạn văn giới thiệu ngắn</label>
                        <textarea 
                            x-model="hero.description" 
                            @input="checkHeroDirty()"
                            rows="3" 
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: 🎁 POPUP KHUYẾN MÃI -->
    <div x-show="activeTab === 'popup'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'popup']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🎁</span>
                            <span>Banner Popup Khuyến Mãi (Tự Động Hiện Khi Khách Vào Web)</span>
                        </h3>
                        <p class="text-xs text-gray-400">Bật/tắt popup chào mừng, ưu đãi freeship hoặc tặng món</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                    <!-- Form Cấu hình Popup -->
                    <div class="md:col-span-7 space-y-4 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Trạng thái hiển thị Popup</label>
                            <select 
                                name="popup_enabled" 
                                x-model="popupState.enabled"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none cursor-pointer"
                            >
                                <option value="1">🟢 Đang BẬT hiển thị Popup</option>
                                <option value="0">⚫ TẮT Popup (Không hiển thị)</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tiêu đề Popup</label>
                            <input 
                                type="text" 
                                name="popup_title" 
                                x-model="popupState.title"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Nội dung ưu đãi chi tiết</label>
                            <textarea 
                                name="popup_description" 
                                x-model="popupState.description"
                                rows="3" 
                                class="w-full px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Chữ nút bấm (CTA)</label>
                                <input 
                                    type="text" 
                                    name="popup_cta_text" 
                                    x-model="popupState.ctaText"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                                >
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-gray-700">Link chuyển hướng khi click</label>
                                <input 
                                    type="text" 
                                    name="popup_cta_url" 
                                    x-model="popupState.ctaUrl"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                                >
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Ảnh Banner Popup (Tùy chọn tải lên)</label>
                            <input 
                                type="file" 
                                name="popup_banner_file" 
                                @change="handlePopupFile"
                                accept="image/*"
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium cursor-pointer"
                            >
                        </div>
                    </div>

                    <!-- Live Preview Popup Box -->
                    <div class="md:col-span-5 bg-gray-100 p-4 rounded-2xl border border-gray-200 space-y-3">
                        <span class="text-[10px] font-black uppercase text-gray-500 block tracking-wider">👁️ Mô phỏng Popup hiển thị:</span>
                        
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200 space-y-3 p-4 text-center">
                            <template x-if="popupState.filePreview || popupState.imageUrl">
                                <img :src="popupState.filePreview || popupState.imageUrl" class="w-full h-36 object-cover rounded-xl mb-2">
                            </template>
                            <h4 class="font-black text-sm text-gray-900" x-text="popupState.title"></h4>
                            <p class="text-xs text-gray-600 leading-relaxed" x-text="popupState.description"></p>
                            <button type="button" class="w-full py-2.5 rounded-xl bg-red-600 text-white font-black text-xs shadow-sm" x-text="popupState.ctaText"></button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Cấu Hình Popup
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- TAB 4: 🛡️ 3 CAM KẾT CHẤT LƯỢNG -->
    <div x-show="activeTab === 'benefits'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🛡️</span>
                    <span>3 Cam Kết Chất Lượng Thương Hiệu</span>
                </h3>
                <p class="text-xs text-gray-400">Hiển thị ngay bên dưới Banner Hero để tăng độ tin tưởng cho khách hàng</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="(b, idx) in benefits" :key="b.id">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-gray-400 uppercase" x-text="'Cam kết #' + (idx + 1)"></span>
                            <input 
                                type="text" 
                                x-model="b.icon" 
                                class="w-10 h-10 text-center rounded-xl bg-white border border-gray-200 text-base shadow-2xs font-bold"
                                title="Icon emoji"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-gray-700">Tiêu đề cam kết</label>
                            <input 
                                type="text" 
                                x-model="b.title" 
                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-black text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold text-gray-700">Mô tả cam kết</label>
                            <textarea 
                                x-model="b.description" 
                                rows="2" 
                                class="w-full px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-medium text-gray-700 outline-none focus:border-red-500"
                            ></textarea>
                        </div>

                        <button 
                            type="button" 
                            @click="saveBenefit(b)"
                            class="w-full py-2 rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-xs transition-colors cursor-pointer"
                        >
                            💾 Lưu Cam Kết Này
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 5: 💬 ĐÁNH GIÁ KHÁCH HÀNG (TESTIMONIALS) -->
    <div x-show="activeTab === 'testimonials'" class="space-y-4" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span>💬</span>
                        <span>Đánh Giá Khách Hàng (Customer Reviews)</span>
                    </h3>
                    <p class="text-xs text-gray-400">Các feedback khen ngon chân thực xuất hiện ở phần chân website</p>
                </div>
                <button 
                    type="button" 
                    @click="openCreateReviewModal()"
                    class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                >
                    + Thêm Đánh Giá Mới
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="r in testimonials" :key="r.id">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200/80 shadow-2xs space-y-2 relative">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-black text-xs flex items-center justify-center" x-text="r.customer_name.charAt(0)"></span>
                                <div>
                                    <h4 class="font-black text-xs text-gray-900" x-text="r.customer_name"></h4>
                                    <span class="text-[10px] text-gray-400" x-text="r.location || 'Hà Nội'"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1">
                                <button 
                                    type="button" 
                                    @click="openEditReviewModal(r)"
                                    class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-600 text-xs font-bold"
                                >
                                    ✏️
                                </button>
                                <button 
                                    type="button" 
                                    @click="deleteReview(r)"
                                    class="p-1.5 rounded-lg hover:bg-rose-100 text-rose-600 text-xs font-bold"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div class="text-amber-500 text-xs tracking-widest">
                            ★★★★★
                        </div>

                        <p class="text-xs text-gray-700 italic" x-text="'“' + r.content + '”'"></p>

                        <div class="pt-1 border-t border-gray-200/60 text-[10px] text-gray-500 font-semibold" x-text="'Món yêu thích: ' + r.favorite_dish"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 6: 🔻 CHÂN TRANG (FOOTER) -->
    <div x-show="activeTab === 'footer'" class="space-y-4" x-cloak>
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_redirect_to" value="{{ route('admin.content.index', ['tab' => 'footer']) }}">

            <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <span>🔻</span>
                            <span>Cấu Hình Chân Trang (Footer)</span>
                        </h3>
                        <p class="text-xs text-gray-400">Địa chỉ cơ sở, số hotline đặt món, giờ mở cửa và link mạng xã hội</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Địa chỉ Cơ sở 1</label>
                        <input 
                            type="text" 
                            name="footer_address_1" 
                            value="{{ $settings['footer_address_1'] ?? '123 Đường Cầu Giấy, Q. Cầu Giấy, Hà Nội' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Địa chỉ Cơ sở 2 (Tùy chọn)</label>
                        <input 
                            type="text" 
                            name="footer_address_2" 
                            value="{{ $settings['footer_address_2'] ?? '456 Đường Nguyễn Trãi, Q. Thanh Xuân, Hà Nội' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Giờ mở cửa hiển thị Footer</label>
                        <input 
                            type="text" 
                            name="footer_hours" 
                            value="{{ $settings['footer_hours'] ?? '10:00 - 22:30 (Hằng ngày)' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Bản quyền Website (Copyright)</label>
                        <input 
                            type="text" 
                            name="footer_copyright" 
                            value="{{ $settings['footer_copyright'] ?? '© 2026 GAO Chicken. All rights reserved.' }}"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <!-- Mạng Xã Hội -->
                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Link Fanpage Facebook</label>
                        <input 
                            type="text" 
                            name="social_facebook" 
                            value="{{ $settings['social_facebook'] ?? 'https://facebook.com/gaochicken' }}"
                            placeholder="https://facebook.com/..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-gray-700">Link Kênh TikTok</label>
                        <input 
                            type="text" 
                            name="social_tiktok" 
                            value="{{ $settings['social_tiktok'] ?? 'https://tiktok.com/@gaochicken' }}"
                            placeholder="https://tiktok.com/@..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 font-mono text-gray-800 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                    >
                        💾 Lưu Chân Trang (Footer)
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- MODAL DRAWER TẠO / SỬA ĐÁNH GIÁ (TESTIMONIAL) -->
    <div 
        x-show="showReviewDrawer" 
        class="fixed inset-0 z-50 overflow-hidden" 
        x-cloak
    >
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="showReviewDrawer = false"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white p-6 shadow-2xl space-y-4 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="font-black text-sm text-gray-900 uppercase" x-text="isEditingReview ? '✏️ Sửa Đánh Giá' : '➕ Thêm Đánh Giá Mới'"></h3>
                        <button @click="showReviewDrawer = false" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Tên khách hàng <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="reviewForm.customer_name" 
                                placeholder="VD: Hoàng Yến" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Khu vực / Quận</label>
                            <input 
                                type="text" 
                                x-model="reviewForm.location" 
                                placeholder="VD: Cầu Giấy, Hà Nội" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Món ăn yêu thích</label>
                            <input 
                                type="text" 
                                x-model="reviewForm.favorite_dish" 
                                placeholder="VD: Cơm Gà Sốt Cay Hàn" 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-bold text-gray-900 outline-none focus:border-red-500"
                            >
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-gray-700">Nội dung đánh giá <span class="text-red-500">*</span></label>
                            <textarea 
                                x-model="reviewForm.content" 
                                rows="4" 
                                placeholder="Cảm nhận thực tế của khách hàng..." 
                                class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 font-medium text-gray-900 outline-none focus:border-red-500"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="showReviewDrawer = false" 
                        class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs"
                    >
                        Hủy
                    </button>
                    <button 
                        type="button" 
                        @click="saveReview()" 
                        class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase shadow-sm"
                    >
                        Lưu Đánh Giá
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
