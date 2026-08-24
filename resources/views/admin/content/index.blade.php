@extends('layouts.admin')

@section('title', 'Quản Lý Nội Dung Trang Chủ')
@section('page_title', '📢 Quản Lý Nội Dung Trang Chủ')

@section('content')
<div 
    class="max-w-5xl space-y-5 pb-20" 
    x-data="{
        toastMessage: '',
        isDirty: false,
        showReviewDrawer: false,
        isEditingReview: false,
        activeMenuReviewId: null,

        // Banner Hero State
        hero: {
            badge: '{{ addslashes($hero->badge ?? '🔥 GÀ CHIÊN + SỐT ĐẬM VỊ!') }}',
            title: '{{ addslashes($hero->title ?? 'GÀ GIÒN.') }}',
            title_highlight: '{{ addslashes($hero->title_highlight ?? 'SỐT ĐẬM.') }}',
            stat_number: '{{ addslashes($hero->stat_number ?? '15.000+') }}',
            stat_label: '{{ addslashes($hero->stat_label ?? 'suất cơm gà phục vụ mỗi tháng') }}',
            description: '{{ addslashes($hero->description ?? 'Chuyên các món gà rán giòn rụm kết hợp cùng 4 loại sốt độc quyền chuẩn vị Hà Nội. Giao hàng nóng hổi tận tay trong 25–40 phút.') }}',
            saved: {}
        },

        // Benefits State (3 cam kết)
        benefits: {{ json_encode($benefits->map(fn($b) => [
            'id' => $b->id,
            'icon' => $b->icon ?: '⚡',
            'title' => $b->title,
            'description' => $b->description,
            'saved_title' => $b->title,
            'saved_description' => $b->description,
            'saved_icon' => $b->icon ?: '⚡'
        ])) }},

        // Testimonials State
        testimonials: {{ json_encode($testimonials->map(fn($t) => [
            'id' => $t->id,
            'customer_name' => $t->customer_name,
            'location' => $t->location,
            'favorite_dish' => $t->favorite_dish ?: 'Cơm Gà Sốt Cay Hàn',
            'rating' => (int)$t->rating,
            'content' => $t->comment ?: $t->content,
        ])) }},

        // Review Form State
        reviewForm: {
            id: null,
            customer_name: '',
            location: '',
            favorite_dish: '',
            rating: 5,
            content: ''
        },

        init() {
            this.hero.saved = { ...this.hero };
        },

        showToast(msg) {
            this.toastMessage = msg;
            setTimeout(() => { this.toastMessage = ''; }, 3500);
        },

        checkDirty() {
            const heroDirty = this.hero.badge !== this.hero.saved.badge ||
                              this.hero.title !== this.hero.saved.title ||
                              this.hero.title_highlight !== this.hero.saved.title_highlight ||
                              this.hero.stat_number !== this.hero.saved.stat_number ||
                              this.hero.stat_label !== this.hero.saved.stat_label ||
                              this.hero.description !== this.hero.saved.description;

            const benefitsDirty = this.benefits.some(b => b.title !== b.saved_title || b.description !== b.saved_description || b.icon !== b.saved_icon);

            this.isDirty = heroDirty || benefitsDirty;
        },

        cancelAllChanges() {
            this.hero = { ...this.hero.saved, saved: { ...this.hero.saved } };
            this.benefits.forEach(b => {
                b.title = b.saved_title;
                b.description = b.saved_description;
                b.icon = b.saved_icon;
            });
            this.isDirty = false;
        },

        async saveAllChanges() {
            try {
                // 1. Save Hero
                const heroRes = await fetch('{{ route('admin.content.hero.update') }}', {
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

                // 2. Save Benefits
                for (const b of this.benefits) {
                    await fetch(`/admin/content/benefit/${b.id}`, {
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
                    b.saved_title = b.title;
                    b.saved_description = b.description;
                    b.saved_icon = b.icon;
                }

                this.hero.saved = { ...this.hero };
                this.isDirty = false;
                this.showToast('Đã cập nhật nội dung trang chủ thành công!');
            } catch (e) {
                alert('Có lỗi khi lưu nội dung. Vui lòng thử lại.');
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
    @keydown.window.ctrl.s.prevent="if(isDirty) saveAllChanges()"
    @keydown.window.cmd.s.prevent="if(isDirty) saveAllChanges()"
    @keydown.window.escape="showReviewDrawer = false"
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
        <span class="text-sm">📣</span>
        <span x-text="toastMessage"></span>
    </div>

    <!-- 1. HEADER (TITLE + XEM TRANG CHỦ) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="space-y-0.5">
            <h1 class="text-lg sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📣 Nội Dung Trang Chủ</span>
            </h1>
            <p class="text-xs text-gray-500 font-medium">
                Quản lý các nội dung đang hiển thị trên trang chủ (Banner Hero, 3 Cam kết & Đánh giá khách hàng).
            </p>
        </div>

        <a 
            href="/" 
            target="_blank" 
            class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold text-xs transition-colors flex items-center gap-1.5 self-start sm:self-center"
        >
            <span>🌐 Xem trang chủ</span>
            <span>↗</span>
        </a>
    </div>

    <!-- 2. SUMMARY STRIP -->
    <div class="bg-white p-3 rounded-2xl border border-gray-200/80 shadow-xs flex flex-wrap items-center justify-between gap-2 text-xs font-bold">
        <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
            <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                🟢 Banner Hero
            </span>
            <span class="px-2.5 py-1 rounded-xl bg-blue-50 text-blue-800 border border-blue-200/60">
                🛡️ 3 Cam kết
            </span>
            <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/60">
                💬 <span x-text="testimonials.length + ' Reviews'"></span>
            </span>
        </div>

        <div>
            <span x-show="isDirty" class="text-amber-600 font-black flex items-center gap-1 text-[11px] animate-pulse" x-cloak>
                <span>●</span>
                <span>Có thay đổi chưa lưu (Ctrl+S)</span>
            </span>
            <span x-show="!isDirty" class="text-gray-400 font-medium text-[11px]">
                ✓ Đã đồng bộ với website
            </span>
        </div>
    </div>

    <!-- 3. BANNER HERO (2-COLUMN: LIVE PREVIEW + FORM) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-3">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🌟</span>
                    <span>Banner Hero</span>
                </h3>
            </div>
            <span class="text-[11px] text-gray-400 font-medium">Khẩu hiệu & số liệu nổi bật đầu trang</span>
        </div>

        <div class="p-4 sm:p-5 pt-1 grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            
            <!-- Live Preview Hero Bên Trái (lg:col-span-5) -->
            <div class="lg:col-span-5 bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white p-5 rounded-2xl shadow-md space-y-3 relative overflow-hidden">
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

            <!-- Form Chỉnh Sửa Bên Phải (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-3">
                
                <!-- Badge -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Huy hiệu nhỏ (Badge)</label>
                    <input 
                        type="text" 
                        x-model="hero.badge" 
                        @input="checkDirty()"
                        placeholder="VD: 🔥 GÀ CHIÊN + SỐT ĐẬM VỊ!"
                        class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <!-- Tiêu đề 1 & Tiêu đề nổi bật (Dòng 2) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tiêu đề chính (Dòng 1)</label>
                        <input 
                            type="text" 
                            x-model="hero.title" 
                            @input="checkDirty()"
                            placeholder="GÀ GIÒN."
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tiêu đề nổi bật (Dòng 2)</label>
                        <input 
                            type="text" 
                            x-model="hero.title_highlight" 
                            @input="checkDirty()"
                            placeholder="SỐT ĐẬM."
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-red-600 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Số liệu</label>
                        <input 
                            type="text" 
                            x-model="hero.stat_number" 
                            @input="checkDirty()"
                            placeholder="15.000+"
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-black text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                        >
                    </div>

                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Đơn vị thống kê</label>
                        <input 
                            type="text" 
                            x-model="hero.stat_label" 
                            @input="checkDirty()"
                            placeholder="suất cơm gà phục vụ mỗi tháng"
                            class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                        >
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Đoạn mô tả giới thiệu</label>
                    <textarea 
                        x-model="hero.description" 
                        @input="checkDirty()"
                        rows="2" 
                        placeholder="Mô tả tóm tắt dịch vụ..."
                        class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none leading-relaxed"
                    ></textarea>
                </div>

            </div>

        </div>
    </div>

    <!-- 4. 3 CAM KẾT DỊCH VỤ (COMPACT EDITABLE CARDS) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-3">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🛡️</span>
                    <span>Cam Kết Dịch Vụ</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold" x-text="benefits.length + ' mục'"></span>
            </div>
            <span class="text-[11px] text-gray-400 font-medium">3 tiêu chuẩn chất lượng hiển thị dưới banner</span>
        </div>

        <div class="p-4 sm:p-5 pt-1 grid grid-cols-1 md:grid-cols-3 gap-4">
            <template x-for="(b, idx) in benefits" :key="b.id">
                <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-400 uppercase tracking-wider" x-text="'Cam kết #' + (idx + 1)"></span>
                        <input 
                            type="text" 
                            x-model="b.icon" 
                            @input="checkDirty()"
                            class="w-8 text-center text-sm bg-white border border-gray-200 rounded-lg p-0.5"
                            title="Icon biểu tượng"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-gray-600">Tiêu đề</label>
                        <input 
                            type="text" 
                            x-model="b.title" 
                            @input="checkDirty()"
                            placeholder="Tiêu đề cam kết..."
                            class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-200 text-xs font-black text-gray-900 focus:border-red-500 outline-none"
                        >
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[11px] font-bold text-gray-600">Mô tả chi tiết</label>
                        <textarea 
                            x-model="b.description" 
                            @input="checkDirty()"
                            rows="2" 
                            placeholder="Chi tiết cam kết..."
                            class="w-full px-3 py-1.5 rounded-xl bg-white border border-gray-200 text-xs font-medium text-gray-700 focus:border-red-500 outline-none leading-relaxed"
                        ></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- 5. ĐÁNH GIÁ KHÁCH HÀNG (REVIEWS 5 SAO) -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden space-y-3">
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                    <span>💬</span>
                    <span>Đánh Giá Khách Hàng</span>
                </h3>
                <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-xs font-bold" x-text="testimonials.length + ' review · 5 sao'"></span>
            </div>

            <button 
                type="button" 
                @click="openCreateReviewModal()"
                class="px-3.5 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold transition-colors flex items-center gap-1 cursor-pointer"
            >
                <span>+ Thêm review</span>
            </button>
        </div>

        <!-- Danh sách Review (Grid 3 cột) -->
        <div class="p-4 sm:p-5 pt-1 grid grid-cols-1 md:grid-cols-3 gap-3.5">
            <template x-for="r in testimonials" :key="r.id">
                <div class="p-4 bg-gray-50/80 hover:bg-white rounded-2xl border border-gray-200 transition-all space-y-2 flex flex-col justify-between relative group">
                    
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-amber-500 font-bold" x-text="'★'.repeat(r.rating) + '☆'.repeat(5 - r.rating)"></span>
                            
                            <!-- Menu 3 chấm -->
                            <div class="relative" @click.stop>
                                <button 
                                    type="button" 
                                    @click="activeMenuReviewId = (activeMenuReviewId === r.id ? null : r.id)"
                                    class="w-6 h-6 rounded-md hover:bg-gray-200 text-gray-500 font-black text-xs flex items-center justify-center transition-colors cursor-pointer"
                                >
                                    ⋮
                                </button>

                                <div 
                                    x-show="activeMenuReviewId === r.id" 
                                    x-transition 
                                    class="absolute right-0 top-full mt-1 w-32 bg-white rounded-xl shadow-xl border border-gray-200 py-1 text-xs text-left z-30 font-medium"
                                    x-cloak
                                >
                                    <button 
                                        type="button" 
                                        @click="openEditReviewModal(r)"
                                        class="w-full px-3 py-1.5 hover:bg-gray-50 text-gray-700 flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <span>✏️</span>
                                        <span>Chỉnh sửa</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        @click="deleteReview(r)"
                                        class="w-full px-3 py-1.5 hover:bg-rose-50 text-rose-600 flex items-center gap-1.5 cursor-pointer font-bold"
                                    >
                                        <span>🗑️</span>
                                        <span>Xoá</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="font-black text-xs text-gray-900 block" x-text="r.customer_name"></span>
                            <span class="text-[11px] text-gray-500 block">📍 <span x-text="r.location"></span></span>
                        </div>

                        <p class="text-[11px] text-gray-500 font-medium">
                            Món: <strong class="text-gray-800" x-text="r.favorite_dish"></strong>
                        </p>

                        <p class="text-xs text-gray-700 italic leading-relaxed pt-1" x-text="'“' + r.content + '”'"></p>
                    </div>

                </div>
            </template>
        </div>
    </div>

    <!-- 6. GLOBAL STICKY SAVE BAR -->
    <div 
        x-show="isDirty" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-2xl py-3 px-4 sm:px-8"
        x-cloak
    >
        <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/70 px-2.5 py-1 rounded-xl animate-pulse">
                    <span>●</span>
                    <span>Có thay đổi nội dung chưa lưu (Ctrl+S)</span>
                </span>
            </div>

            <div class="flex items-center gap-2.5">
                <button 
                    type="button" 
                    @click="cancelAllChanges()"
                    class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors cursor-pointer"
                >
                    Hủy thay đổi
                </button>

                <button 
                    type="button" 
                    @click="saveAllChanges()"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-md hover:scale-[1.01] active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <span>💾</span>
                    <span>Lưu tất cả thay đổi</span>
                </button>
            </div>

        </div>
    </div>

    <!-- 7. DRAWER / MODAL THÊM & SỬA REVIEW -->
    <div 
        x-show="showReviewDrawer" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            
            <div 
                x-show="showReviewDrawer"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs" 
                @click="showReviewDrawer = false"
            ></div>

            <div 
                x-show="showReviewDrawer"
                x-transition:enter="ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-md p-5 sm:p-6 space-y-4 text-xs z-50"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="font-black text-base text-gray-900" x-text="isEditingReview ? '✏️ Chỉnh Sửa Đánh Giá' : '➕ Thêm Đánh Giá Mới'"></h3>
                    <button @click="showReviewDrawer = false" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-bold flex items-center justify-center text-sm cursor-pointer">✕</button>
                </div>

                <!-- Form Fields -->
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Tên khách hàng <span class="text-red-500">*</span></label>
                        <input type="text" x-model="reviewForm.customer_name" placeholder="VD: Minh Anh" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Khu vực / Nghề nghiệp <span class="text-red-500">*</span></label>
                            <input type="text" x-model="reviewForm.location" placeholder="VD: Cầu Giấy, Hà Nội" required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-gray-700">Số sao đánh giá</label>
                            <select x-model="reviewForm.rating" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-amber-500 outline-none cursor-pointer">
                                <option value="5">⭐⭐⭐⭐⭐ (5 sao)</option>
                                <option value="4">⭐⭐⭐⭐ (4 sao)</option>
                                <option value="3">⭐⭐⭐ (3 sao)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Món yêu thích</label>
                        <input type="text" x-model="reviewForm.favorite_dish" placeholder="VD: Cơm Gà Sốt Cay Hàn" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-900 focus:bg-white focus:border-red-500 outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-gray-700">Nội dung đánh giá <span class="text-red-500">*</span></label>
                        <textarea x-model="reviewForm.content" rows="3" placeholder="Lời nhận xét của khách..." required class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-900 focus:bg-white focus:border-red-500 outline-none leading-relaxed"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                    <button type="button" @click="showReviewDrawer = false" class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs cursor-pointer">
                        Hủy
                    </button>
                    <button type="button" @click="saveReview()" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs cursor-pointer">
                        <span x-text="isEditingReview ? 'Lưu thay đổi' : 'Thêm review'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
