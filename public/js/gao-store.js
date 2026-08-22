/**
 * GAO - Gà Sốt & Cơm Hà Nội
 * Alpine.js Global Store & Controller (Dynamic DB-Driven)
 */
function gaoApp() {
    // Read dynamic data injected from Laravel Blade or fallback to empty array
    const initialData = window.GAO_DATA || {};

    const dbCategories = (initialData.categories || []).map(cat => ({
        id: cat.slug,
        name: cat.name,
        icon: cat.icon || '✨',
        count: cat.products_count || 0
    }));

    // Add 'Tất Cả' tab dynamically with total count
    const totalProductCount = (initialData.products || []).length;
    const allCategories = [
        { id: 'all', name: 'Tất Cả', icon: '✨', count: totalProductCount },
        ...dbCategories
    ];

    const dbSauces = (initialData.sauces || []).map(s => ({
        id: s.slug,
        name: s.name,
        icon: s.icon || '🌶️',
        tag: s.tag || '',
        subtitle: s.subtitle || '',
        shortDesc: s.short_desc || '',
        description: s.description || '',
        image: s.image || '',
        price: Number(s.price) || 49000
    }));

    const dbProducts = (initialData.products || []).map(p => ({
        id: 'product-' + p.id,
        db_id: p.id,
        name: p.name,
        category: p.category ? p.category.slug : 'rice',
        tag: p.tag,
        rating: p.rating || 5.0,
        reviewCount: p.review_count || 0,
        description: p.description || '',
        price: Number(p.price),
        original_price: p.original_price ? Number(p.original_price) : null,
        sauce: p.default_sauce || null,
        image: p.image || '',
        is_hot: p.is_hot || false
    }));

    const dbSpiceLevels = (initialData.spiceLevels || []).map(sp => ({
        id: 'spice-' + sp.id,
        name: sp.name,
        desc: sp.description || ''
    }));

    const dbToppings = (initialData.toppings || []).map(t => ({
        id: 'top-' + t.id,
        name: t.name,
        price: Number(t.price),
        icon: t.icon || '🍳'
    }));

    const dbUpsell = (initialData.upsellItems || []).map(u => ({
        id: 'up-' + u.id,
        name: u.name,
        price: Number(u.price),
        image: u.image || '',
        icon: (u.category && u.category.slug === 'drink') ? '🥤' : '🍟'
    }));

    return {
        currentView: 'home', // 'home' or 'menu'
        isCartOpen: false,
        openCustomizeModal: false,
        openCheckoutModal: false,
        openSuccessModal: false,
        isSubmitting: false,
        
        selectedSauceId: dbSauces.length > 0 ? dbSauces[0].id : 'korean_spicy',
        activeCategory: 'all',
        searchQuery: '',

        // Data arrays driven by Database
        categories: allCategories,
        sauceList: dbSauces,
        allMenuItems: dbProducts,
        spiceLevels: dbSpiceLevels,
        availableToppings: dbToppings,
        upsellItems: dbUpsell,

        // Checkout Form state
        checkoutForm: {
            fullName: '',
            phone: '',
            district: 'Quận Cầu Giấy',
            address: '',
            driverNote: '',
            paymentMethod: 'cod' // 'cod', 'momo', 'vnpay', 'zalopay'
        },

        orderSuccessData: {
            orderCode: '',
            totalAmount: 0
        },

        // Customizing item state
        customizingItem: {
            id: '',
            name: 'Cơm Gà Sốt Cay',
            basePrice: 49000,
            image: '',
            rating: '4.9 (384 đánh giá)',
            description: '',
            quantity: 1,
            selectedSauce: 'Sốt Cay Hàn',
            selectedSpiceLevel: 'Cay nhẹ (Chuẩn vị)',
            selectedToppings: [],
            note: ''
        },

        // Hanoi Districts
        districts: [
            'Quận Cầu Giấy',
            'Quận Đống Đa',
            'Quận Hoàn Kiếm',
            'Quận Hai Bà Trưng',
            'Quận Ba Đình',
            'Quận Thanh Xuân',
            'Quận Tây Hồ',
            'Quận Nam Từ Liêm',
            'Quận Bắc Từ Liêm',
            'Quận Hoàng Mai',
            'Quận Long Biên',
            'Quận Hà Đông'
        ],
        
        toast: {
            show: false,
            title: '',
            message: '',
            timeout: null
        },

        // Cart state
        cartItems: [],

        init() {
            // Check URL hash for routing
            if (window.location.hash === '#menu') {
                this.currentView = 'menu';
            }
            window.addEventListener('hashchange', () => {
                if (window.location.hash === '#menu') {
                    this.currentView = 'menu';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    this.currentView = 'home';
                }
            });
        },

        switchView(view) {
            this.currentView = view;
            if (view === 'menu') {
                window.location.hash = 'menu';
            } else {
                window.location.hash = '';
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        get currentSauce() {
            return this.sauceList.find(s => s.id === this.selectedSauceId) || this.sauceList[0] || {};
        },

        get filteredMenuItems() {
            let items = this.allMenuItems;
            if (this.activeCategory !== 'all') {
                items = items.filter(item => item.category === this.activeCategory);
            }
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                items = items.filter(item => item.name.toLowerCase().includes(q) || (item.description && item.description.toLowerCase().includes(q)));
            }
            return items;
        },

        get popularItems() {
            return this.allMenuItems.filter(i => i.is_hot);
        },

        get totalItemsCount() {
            return this.cartItems.reduce((total, item) => total + item.quantity, 0);
        },

        get totalPrice() {
            return this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        get singleCustomizedPrice() {
            let toppingTotal = this.customizingItem.selectedToppings.reduce((sum, topId) => {
                const top = this.availableToppings.find(t => t.id === topId);
                return sum + (top ? top.price : 0);
            }, 0);
            return this.customizingItem.basePrice + toppingTotal;
        },

        get totalCustomizedPrice() {
            return this.singleCustomizedPrice * this.customizingItem.quantity;
        },

        selectSauce(sauce) {
            this.selectedSauceId = sauce.id;
        },

        // Open modal to customize dish before adding
        openCustomize(item) {
            let defaultSauce = this.sauceList.length > 0 ? this.sauceList[0].name : 'Sốt Cay Hàn';
            if (item.sauce) {
                defaultSauce = item.sauce;
            } else if (item.name) {
                if (item.name.includes('Mật Ong')) defaultSauce = 'Sốt Mật Ong';
                else if (item.name.includes('Bơ Tỏi') || item.name.includes('Tỏi')) defaultSauce = 'Sốt Bơ Tỏi';
                else if (item.name.includes('Chua Ngọt')) defaultSauce = 'Sốt Chua Ngọt';
                else if (item.name.includes('Cay')) defaultSauce = 'Sốt Cay Hàn';
            }

            const defaultSpice = this.spiceLevels.length > 1 ? this.spiceLevels[1].name : (this.spiceLevels[0]?.name || 'Cay nhẹ (Chuẩn vị)');

            this.customizingItem = {
                id: item.id || 'dish-' + Date.now(),
                name: item.name || 'Cơm Gà Sốt Cay',
                basePrice: item.price || 49000,
                image: item.image || '',
                rating: item.rating ? (String(item.rating).includes('đánh giá') ? item.rating : `${item.rating} (${item.reviewCount || 384} đánh giá)`) : '4.9 (384 đánh giá)',
                description: item.description || '',
                quantity: 1,
                selectedSauce: defaultSauce,
                selectedSpiceLevel: defaultSpice,
                selectedToppings: [],
                note: ''
            };
            this.openCustomizeModal = true;
        },

        // Open modal when user chooses from the Sauce Section
        openCustomizeFromSauce(sauce) {
            const defaultSpice = this.spiceLevels.length > 1 ? this.spiceLevels[1].name : (this.spiceLevels[0]?.name || 'Cay nhẹ (Chuẩn vị)');

            this.customizingItem = {
                id: 'dish-sauce-' + sauce.id,
                name: 'Cơm Gà ' + sauce.name,
                basePrice: sauce.price || 49000,
                image: sauce.image,
                rating: '4.9 (384 đánh giá)',
                description: sauce.description || 'Đùi gà chiên giòn rụm phủ đẫm sốt thơm ngon, ăn kèm cơm dẻo và dưa chua.',
                quantity: 1,
                selectedSauce: sauce.name,
                selectedSpiceLevel: defaultSpice,
                selectedToppings: [],
                note: ''
            };
            this.openCustomizeModal = true;
        },

        // Toggle topping checkbox
        toggleTopping(toppingId) {
            const idx = this.customizingItem.selectedToppings.indexOf(toppingId);
            if (idx > -1) {
                this.customizingItem.selectedToppings.splice(idx, 1);
            } else {
                this.customizingItem.selectedToppings.push(toppingId);
            }
        },

        // Confirm customization and immediately open Cart Drawer!
        confirmAddToCart() {
            const toppingNames = this.customizingItem.selectedToppings.map(id => {
                const t = this.availableToppings.find(item => item.id === id);
                return t ? t.name : '';
            }).filter(Boolean);

            const cartItem = {
                id: 'custom-' + Date.now(),
                name: this.customizingItem.name,
                price: this.singleCustomizedPrice,
                quantity: this.customizingItem.quantity,
                sauce: this.customizingItem.selectedSauce,
                spiceLevel: this.customizingItem.selectedSpiceLevel,
                toppings: toppingNames,
                note: this.customizingItem.note,
                image: this.customizingItem.image
            };

            this.cartItems.unshift(cartItem);
            
            // Close customize modal
            this.openCustomizeModal = false;

            // Immediately redirect/open Cart Drawer
            this.isCartOpen = true;
            this.showToast('Đã thêm vào giỏ hàng!', cartItem.name);
        },

        // Quick add for upsell / combos -> directly goes to cart as well
        addToCartDirect(item) {
            const existing = this.cartItems.find(i => i.name === item.name);
            if (existing) {
                existing.quantity++;
            } else {
                this.cartItems.push({
                    id: item.id || ('item-' + Date.now()),
                    name: item.name,
                    price: item.price,
                    image: item.image,
                    quantity: 1,
                    sauce: null,
                    spiceLevel: null,
                    toppings: []
                });
            }
            this.isCartOpen = true;
            this.showToast('Đã thêm vào giỏ!', item.name);
        },

        removeItem(index) {
            this.cartItems.splice(index, 1);
        },

        incrementItem(index) {
            this.cartItems[index].quantity++;
        },

        decrementItem(index) {
            if (this.cartItems[index].quantity > 1) {
                this.cartItems[index].quantity--;
            } else {
                this.removeItem(index);
            }
        },

        // Open Checkout Quick Modal (Matching Screenshot 5 & 6)
        openCheckout() {
            if (this.cartItems.length === 0) {
                alert('Giỏ hàng đang trống, vui lòng chọn món trước khi thanh toán!');
                return;
            }
            this.isCartOpen = false;
            this.openCheckoutModal = true;
        },

        // Submit Order logic - Sends AJAX to Laravel Backend API to save in MySQL Database
        async submitOrder() {
            if (!this.checkoutForm.fullName.trim()) {
                alert('Vui lòng nhập Họ và tên!');
                return;
            }
            if (!this.checkoutForm.phone.trim()) {
                alert('Vui lòng nhập Số điện thoại nhận hàng!');
                return;
            }
            if (!this.checkoutForm.address.trim()) {
                alert('Vui lòng nhập Địa chỉ chi tiết (số nhà, ngõ, tên toà nhà)!');
                return;
            }

            this.isSubmitting = true;

            const payload = {
                fullName: this.checkoutForm.fullName,
                phone: this.checkoutForm.phone,
                district: this.checkoutForm.district,
                address: this.checkoutForm.address,
                driverNote: this.checkoutForm.driverNote,
                paymentMethod: this.checkoutForm.paymentMethod,
                items: this.cartItems.map(i => ({
                    name: i.name,
                    price: i.price,
                    quantity: i.quantity,
                    sauce: i.sauce || null,
                    spiceLevel: i.spiceLevel || null,
                    toppings: i.toppings || [],
                    note: i.note || null
                }))
            };

            try {
                const response = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.orderSuccessData = {
                        orderCode: data.orderCode,
                        totalAmount: data.totalAmount
                    };

                    // Close checkout modal & Open success confirmation modal
                    this.openCheckoutModal = false;
                    this.openSuccessModal = true;

                    // Clear cart
                    this.cartItems = [];
                } else {
                    alert('Lỗi đặt hàng: ' + (data.message || 'Vui lòng thử lại sau!'));
                }
            } catch (err) {
                console.error('Submit order error:', err);
                alert('Có lỗi kết nối máy chủ khi gửi đơn hàng, vui lòng thử lại!');
            } finally {
                this.isSubmitting = false;
            }
        },

        showToast(title, message) {
            this.toast.title = title;
            this.toast.message = message;
            this.toast.show = true;
            if (this.toast.timeout) clearTimeout(this.toast.timeout);
            this.toast.timeout = setTimeout(() => {
                this.toast.show = false;
            }, 2500);
        },

        formatCurrency(val) {
            return new Intl.NumberFormat('vi-VN').format(val || 0) + 'đ';
        },

        scrollToOrderSection() {
            if (this.currentView === 'menu') {
                const el = document.getElementById('menu-grid');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            } else {
                const el = document.getElementById('popular');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            }
        }
    };
}
