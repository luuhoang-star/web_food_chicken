/**
 * GAO - Gà Sốt & Cơm Hà Nội
 * Alpine.js Global Store & Controller (Dynamic DB-Driven with Standalone Sauce Purchasing & Item Types)
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
    const rawProducts = (initialData.allProducts && initialData.allProducts.length > 0) 
        ? initialData.allProducts 
        : (initialData.products || []);

    const totalProductCount = rawProducts.length;
    const popularCount = rawProducts.filter(p => (p.tag === 'BEST SELLER' || (Number(p.sold_count) || 0) > 0)).length || Math.min(8, totalProductCount);
    const allCategories = [
        { id: 'all', name: 'Tất Cả', icon: '✨', count: totalProductCount },
        { id: 'popular', name: 'Bán Chạy', icon: '🔥', count: popularCount },
        ...dbCategories
    ];

    const dbSauces = (initialData.sauces || []).map(s => ({
        id: s.id,
        slug: s.slug,
        name: s.name,
        icon: s.icon || '🌶️',
        tag: s.tag || '',
        subtitle: s.subtitle || '',
        shortDesc: s.short_desc || '',
        description: s.description || '',
        image: s.image || '',
        price: Number(s.price) || 10000,
        is_available: s.is_available ?? true
    }));

    const dbProducts = rawProducts.map(p => {
        const catSlug = p.category ? p.category.slug : 'rice';
        const sauceSelection = p.sauce_selection || (catSlug === 'combo' ? 'required' : (['rice', 'chicken'].includes(catSlug) ? 'fixed' : 'none'));
        const hasSauce = sauceSelection !== 'none';

        let matchedSauce = null;
        if (hasSauce) {
            if (p.sauce_id) {
                matchedSauce = dbSauces.find(s => s.id === p.sauce_id);
            }
            if (!matchedSauce && p.default_sauce) {
                matchedSauce = dbSauces.find(s => s.name.toLowerCase() === p.default_sauce.toLowerCase());
            }
            if (!matchedSauce && p.name) {
                matchedSauce = dbSauces.find(s => p.name.toLowerCase().includes(s.name.toLowerCase()));
            }
        }

        return {
            id: 'product-' + p.id,
            db_id: p.id,
            sauce_id: matchedSauce ? matchedSauce.id : (p.sauce_id || null),
            sauce_slug: matchedSauce ? matchedSauce.slug : null,
            sauce_selection: sauceSelection,
            is_sauce_choice: sauceSelection === 'required',
            supported_sauce_ids: (p.sauces || []).map(s => s.id),
            name: p.name,
            slug: p.slug,
            category: catSlug,
            has_sauce: hasSauce,
            tag: ['BEST SELLER', 'TIẾT KIỆM'].includes(p.tag) ? p.tag : null,
            rating: p.rating || 5.0,
            reviewCount: p.review_count || 0,
            description: p.description || '',
            price: Number(p.price),
            original_price: p.original_price ? Number(p.original_price) : null,
            sauce: matchedSauce ? matchedSauce.name : (p.default_sauce || (hasSauce && sauceSelection === 'fixed' ? 'Sốt Cay Hàn' : null)),
            image: p.image || '',
            sold_count: Number(p.sold_count) || 0,
            order: Number(p.order) || 99
        };
    });

    const getSortedPopularProducts = (products) => {
        return [...products].sort((a, b) => {
            const aTagScore = a.tag === 'BEST SELLER' ? 1 : 0;
            const bTagScore = b.tag === 'BEST SELLER' ? 1 : 0;
            if (bTagScore !== aTagScore) {
                return bTagScore - aTagScore;
            }
            const aSold = a.sold_count || 0;
            const bSold = b.sold_count || 0;
            if (bSold !== aSold) {
                return bSold - aSold;
            }
            return (a.order || 0) - (b.order || 0);
        });
    };

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

    // Load persisted cart from localStorage
    let savedCart = [];
    try {
        const stored = localStorage.getItem('gao_cart');
        if (stored) {
            savedCart = JSON.parse(stored);
        }
    } catch (e) {
        console.error('Error loading cart from storage', e);
    }

    return {
        currentView: 'home',
        isCartOpen: false,
        openCustomizeModal: false,
        openCheckoutModal: false,
        isCheckoutOpen: false,
        openSuccessModal: false,
        isSubmitting: false,
        
        selectedSauceId: dbSauces.length > 0 ? dbSauces[0].id : 1,
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
            paymentMethod: 'cod', // 'cod', 'momo', 'bank_transfer', 'zalopay'
            couponCode: '',
            discount: 0
        },

        orderSuccessData: {
            orderCode: '',
            totalAmount: 0
        },

        // Customizing item state
        customizingItem: {
            id: '',
            db_id: null,
            category: 'rice',
            hasSauce: true,
            name: 'Cơm Gà',
            basePrice: 49000,
            image: '',
            rating: '4.9 (384 đánh giá)',
            description: '',
            quantity: 1,
            selectedSauce: 'Sốt Cay Hàn',
            selectedSpiceLevel: 'Cay nhẹ (Chuẩn vị)',
            selectedToppings: [],
            selectedSides: [],
            selectedDrinks: [],
            extraSauces: {}, // e.g. { 'sot-cay-han': 2, 'sot-mat-ong': 1 }
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
        cartItems: savedCart,

        init() {
            this.saveCart();
        },

        saveCart() {
            try {
                localStorage.setItem('gao_cart', JSON.stringify(this.cartItems));
            } catch (e) {
                console.error('Error saving cart to storage', e);
            }
        },

        get currentSauce() {
            return this.sauceList.find(s => s.id === this.selectedSauceId) || this.sauceList[0] || {};
        },

        get filteredMenuItems() {
            let items = this.allMenuItems;
            if (this.activeCategory === 'popular') {
                const sorted = getSortedPopularProducts(this.allMenuItems);
                const populars = sorted.filter(i => (i.tag === 'BEST SELLER' || (i.sold_count && i.sold_count > 0)));
                items = populars.length > 0 ? populars : sorted.slice(0, 8);
            } else if (this.activeCategory !== 'all') {
                items = items.filter(item => item.category === this.activeCategory);
            }
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                items = items.filter(item => item.name.toLowerCase().includes(q) || (item.description && item.description.toLowerCase().includes(q)));
            }
            return items;
        },

        get popularItems() {
            const sorted = getSortedPopularProducts(this.allMenuItems);
            const populars = sorted.filter(i => (i.tag === 'BEST SELLER' || (i.sold_count && i.sold_count > 0)));
            if (populars.length >= 4) {
                return populars.slice(0, 8);
            }
            return sorted.slice(0, 8);
        },

        get totalItemsCount() {
            return this.cartItems.reduce((total, item) => total + item.quantity, 0);
        },

        get totalPrice() {
            return this.cartItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        get availableSides() {
            return this.allMenuItems.filter(i => i.category === 'side' && !i.name.includes('Trứng'));
        },

        get availableDrinks() {
            return this.allMenuItems.filter(i => i.category === 'drink');
        },

        get singleCustomizedPrice() {
            const toppingTotal = (this.customizingItem.selectedToppings || []).reduce((sum, topId) => {
                const top = this.availableToppings.find(t => t.id === topId);
                return sum + (top ? top.price : 0);
            }, 0);
            const sideTotal = (this.customizingItem.selectedSides || []).reduce((sum, sideId) => {
                const side = this.availableSides.find(s => s.id === sideId);
                return sum + (side ? side.price : 0);
            }, 0);
            const drinkTotal = (this.customizingItem.selectedDrinks || []).reduce((sum, drinkId) => {
                const drink = this.availableDrinks.find(d => d.id === drinkId);
                return sum + (drink ? drink.price : 0);
            }, 0);
            return this.customizingItem.basePrice + toppingTotal + sideTotal + drinkTotal;
        },

        get totalExtraSaucePrice() {
            let total = 0;
            for (const [slug, qty] of Object.entries(this.customizingItem.extraSauces || {})) {
                if (qty > 0) {
                    const sauce = this.sauceList.find(s => s.slug === slug);
                    const price = sauce ? sauce.price : 10000;
                    total += (price * qty);
                }
            }
            return total;
        },

        get totalCustomizedPrice() {
            return (this.singleCustomizedPrice * this.customizingItem.quantity) + this.totalExtraSaucePrice;
        },

        // Helper for extra sauces in customize modal
        getExtraSauceQty(slug) {
            return this.customizingItem.extraSauces[slug] || 0;
        },

        incrementExtraSauce(slug) {
            const current = this.customizingItem.extraSauces[slug] || 0;
            this.customizingItem.extraSauces = {
                ...this.customizingItem.extraSauces,
                [slug]: current + 1
            };
        },

        decrementExtraSauce(slug) {
            const current = this.customizingItem.extraSauces[slug] || 0;
            if (current > 1) {
                this.customizingItem.extraSauces = {
                    ...this.customizingItem.extraSauces,
                    [slug]: current - 1
                };
            } else if (current === 1) {
                const updated = { ...this.customizingItem.extraSauces };
                delete updated[slug];
                this.customizingItem.extraSauces = updated;
            }
        },

        // 1. ADD STANDALONE SAUCE TO CART (10.000đ/phần, no chicken dish required)
        addSauceToCart(sauce, qty = 1) {
            const addQty = Math.max(1, Number(qty) || 1);
            
            // Check if this exact sauce is already in the cart
            const existing = this.cartItems.find(i => 
                (i.item_type === 'sauce' && (i.sauce_id === sauce.id || i.name === sauce.name))
            );

            if (existing) {
                existing.quantity += addQty;
            } else {
                this.cartItems.unshift({
                    id: 'sauce-' + sauce.id + '-' + Date.now(),
                    item_type: 'sauce',
                    product_id: null,
                    sauce_id: sauce.id,
                    name: sauce.name,
                    price: Number(sauce.price) || 10000,
                    quantity: addQty,
                    sauce: sauce.name,
                    spiceLevel: null,
                    toppings: [],
                    note: 'Hũ sốt mua thêm',
                    image: sauce.image || ''
                });
            }

            this.saveCart();
            this.isCartOpen = true;
            this.showToast('Đã thêm sốt vào giỏ hàng!', `${sauce.name} (+${addQty})`);
        },

        openCustomizeFromSauce(sauce) {
            if (sauce && sauce.name) {
                window.location.href = '/menu?q=' + encodeURIComponent(sauce.name);
            } else {
                window.location.href = '/menu';
            }
        },

        // 2. Open modal to customize dish before adding
        openCustomize(item) {
            // For drinks or sides without customization, quick add directly
            if (['drink', 'side'].includes(item.category)) {
                this.addToCartDirect(item);
                return;
            }

            let defaultSauce = this.sauceList.length > 0 ? this.sauceList[0].name : 'Sốt Cay Hàn';
            if (item.sauce) {
                defaultSauce = item.sauce;
            } else if (item.name && item.name.includes('Mật Ong')) {
                defaultSauce = 'Sốt Mật Ong';
            } else if (item.name && (item.name.includes('Bơ Tỏi') || item.name.includes('Tỏi'))) {
                defaultSauce = 'Sốt Bơ Tỏi';
            } else if (item.name && item.name.includes('Chua Ngọt')) {
                defaultSauce = 'Sốt Chua Ngọt';
            } else if (item.name && item.name.includes('Cay')) {
                defaultSauce = 'Sốt Cay Hàn';
            } else {
                // Auto pre-select sauce if user filtered by a sauce name
                const q = (new URLSearchParams(window.location.search).get('q') || '').toLowerCase();
                if (q) {
                    const matched = this.sauceList.find(s => q.includes(s.slug) || q.includes(s.name.toLowerCase()) || s.name.toLowerCase().includes(q));
                    if (matched) {
                        defaultSauce = matched.name;
                    }
                }
            }

            const defaultSpice = this.spiceLevels.length > 1 ? this.spiceLevels[1].name : (this.spiceLevels[0]?.name || 'Cay nhẹ (Chuẩn vị)');

            const catSlug = (typeof item.category === 'object' && item.category) ? item.category.slug : (item.category || 'rice');
            const isCombo = catSlug === 'combo';
            const isSauceChoice = isCombo || Boolean(item.is_sauce_choice);

            this.customizingItem = {
                id: item.id || 'dish-' + Date.now(),
                db_id: item.db_id || null,
                category: catSlug,
                is_sauce_choice: isSauceChoice,
                hasSauce: ['rice', 'chicken', 'combo'].includes(catSlug),
                name: item.name || 'Món ăn',
                basePrice: Number(item.price) || 45000,
                image: item.image || '',
                rating: item.rating ? (String(item.rating).includes('đánh giá') ? item.rating : `${item.rating} (${item.reviewCount || 384} đánh giá)`) : '4.9 (384 đánh giá)',
                description: item.description || '',
                quantity: 1,
                selectedSauce: defaultSauce,
                selectedSpiceLevel: defaultSpice,
                selectedToppings: [],
                selectedSides: [],
                selectedDrinks: [],
                extraSauces: {},
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

        // Toggle side checkbox
        toggleSide(sideId) {
            if (!this.customizingItem.selectedSides) {
                this.customizingItem.selectedSides = [];
            }
            const idx = this.customizingItem.selectedSides.indexOf(sideId);
            if (idx > -1) {
                this.customizingItem.selectedSides.splice(idx, 1);
            } else {
                this.customizingItem.selectedSides.push(sideId);
            }
        },

        // Toggle drink checkbox
        toggleDrink(drinkId) {
            if (!this.customizingItem.selectedDrinks) {
                this.customizingItem.selectedDrinks = [];
            }
            const idx = this.customizingItem.selectedDrinks.indexOf(drinkId);
            if (idx > -1) {
                this.customizingItem.selectedDrinks.splice(idx, 1);
            } else {
                this.customizingItem.selectedDrinks.push(drinkId);
            }
        },

        // Confirm dish customization and add Product (and extra sauces) to cart
        confirmAddToCart() {
            const toppingNames = (this.customizingItem.selectedToppings || []).map(id => {
                const t = this.availableToppings.find(item => item.id === id);
                return t ? t.name : '';
            }).filter(Boolean);

            const sideNames = (this.customizingItem.selectedSides || []).map(id => {
                const s = this.availableSides.find(item => item.id === id);
                return s ? s.name : '';
            }).filter(Boolean);

            const drinkNames = (this.customizingItem.selectedDrinks || []).map(id => {
                const d = this.availableDrinks.find(item => item.id === id);
                return d ? d.name : '';
            }).filter(Boolean);

            const allOptions = [...toppingNames, ...sideNames, ...drinkNames];

            // 1. Add Main Product
            const cartItem = {
                id: 'custom-prod-' + Date.now(),
                item_type: 'product',
                product_id: this.customizingItem.db_id || null,
                sauce_id: null,
                name: this.customizingItem.name,
                price: this.singleCustomizedPrice,
                quantity: this.customizingItem.quantity,
                sauce: this.customizingItem.hasSauce ? this.customizingItem.selectedSauce : null,
                spiceLevel: this.customizingItem.hasSauce ? this.customizingItem.selectedSpiceLevel : null,
                toppings: allOptions,
                note: this.customizingItem.note,
                image: this.customizingItem.image
            };

            this.cartItems.unshift(cartItem);

            // 2. Add any Extra Sauces chosen
            for (const [slug, extraQty] of Object.entries(this.customizingItem.extraSauces || {})) {
                if (extraQty > 0) {
                    const sauce = this.sauceList.find(s => s.slug === slug);
                    if (sauce) {
                        const existingSauceItem = this.cartItems.find(i => 
                            i.item_type === 'sauce' && (i.sauce_id === sauce.id || i.name === sauce.name)
                        );
                        if (existingSauceItem) {
                            existingSauceItem.quantity += extraQty;
                        } else {
                            this.cartItems.push({
                                id: 'sauce-' + sauce.id + '-' + Date.now(),
                                item_type: 'sauce',
                                product_id: null,
                                sauce_id: sauce.id,
                                name: sauce.name,
                                price: Number(sauce.price) || 10000,
                                quantity: extraQty,
                                sauce: sauce.name,
                                spiceLevel: null,
                                toppings: [],
                                note: 'Hũ sốt mua thêm',
                                image: sauce.image || ''
                            });
                        }
                    }
                }
            }

            this.saveCart();
            
            this.openCustomizeModal = false;
            this.isCartOpen = true;
            this.showToast('Đã thêm vào giỏ hàng!', cartItem.name);
        },

        // Quick add for upsell / combos / sides / drinks -> directly goes to cart
        addToCartDirect(item) {
            const itemType = (item.name && item.name.toUpperCase().includes('COMBO')) ? 'combo' : 'product';
            const existing = this.cartItems.find(i => i.name === item.name && i.item_type === itemType);
            
            if (existing) {
                existing.quantity++;
            } else {
                this.cartItems.push({
                    id: item.id || ('item-' + Date.now()),
                    item_type: itemType,
                    product_id: item.db_id || null,
                    sauce_id: null,
                    name: item.name,
                    price: item.price,
                    image: item.image,
                    quantity: 1,
                    sauce: null,
                    spiceLevel: null,
                    toppings: []
                });
            }
            this.saveCart();
            this.isCartOpen = true;
            this.showToast('Đã thêm vào giỏ!', item.name);
        },

        removeItem(index) {
            this.cartItems.splice(index, 1);
            this.saveCart();
        },

        incrementItem(index) {
            this.cartItems[index].quantity++;
            this.saveCart();
        },

        decrementItem(index) {
            if (this.cartItems[index].quantity > 1) {
                this.cartItems[index].quantity--;
            } else {
                this.removeItem(index);
            }
            this.saveCart();
        },

        // Open Checkout Modal
        openCheckout() {
            if (this.cartItems.length === 0) {
                alert('Giỏ hàng đang trống, vui lòng chọn món trước khi thanh toán!');
                return;
            }
            this.isCartOpen = false;
            this.openCheckoutModal = true;
            this.isCheckoutOpen = true;
        },

        // Submit Order logic - Sends distinct item_type (product, sauce, combo) to API
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
                couponCode: this.checkoutForm.couponCode || null,
                discount: Number(this.checkoutForm.discount) || 0,
                items: this.cartItems.map(i => ({
                    item_type: i.item_type || 'product',
                    product_id: i.product_id || null,
                    sauce_id: i.sauce_id || null,
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

                    this.openCheckoutModal = false;
                    this.openSuccessModal = true;

                    // Clear cart
                    this.cartItems = [];
                    this.saveCart();
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
        }
    };
}
