<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        gao: {
                            50: '#fff5f5',
                            100: '#ffe3e3',
                            200: '#ffc9c9',
                            300: '#ffa8a8',
                            400: '#fa5252',
                            500: '#e03120',
                            600: '#d12413',
                            700: '#b81c0c',
                            800: '#96170a',
                            900: '#7a1409',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(224, 49, 32, 0.12)',
                        'card': '0 4px 25px 0 rgba(0, 0, 0, 0.05)',
                        'floating': '0 20px 40px -10px rgba(209, 36, 19, 0.3)',
                    }
                }
            }
        }
    </script>

    <!-- JAVASCRIPT STATE STORE FOR FULL LOGIC -->
    <script>
        function gaoApp() {
            return {
                currentView: 'home', // 'home' or 'menu'
                isCartOpen: false,
                openCustomizeModal: false,
                openCheckoutModal: false,
                openSuccessModal: false,
                
                selectedSauceId: 'korean_spicy',
                activeCategory: 'all',
                searchQuery: '',

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
                    image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80',
                    rating: '4.9 (384 đánh giá)',
                    description: 'Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.',
                    quantity: 1,
                    selectedSauce: 'Sốt Cay Hàn',
                    selectedSpiceLevel: 'Cay nhẹ (Chuẩn vị)',
                    selectedToppings: [],
                    note: ''
                },

                // Topping options with prices
                availableToppings: [
                    { id: 'egg', name: 'Trứng Ốp La Lòng Đào', price: 10000, icon: '🍳' },
                    { id: 'cheese', name: 'Phô Mai Mozzarella Kéo Sợi', price: 15000, icon: '🧀' },
                    { id: 'fries', name: 'Khoai Tây Chiên Giòn', price: 20000, icon: '🍟' },
                    { id: 'extra_sauce', name: 'Thêm Sốt Chấm Riêng', price: 8000, icon: '🥣' },
                ],

                // Spice levels
                spiceLevels: [
                    { id: 'none', name: 'Không cay', desc: 'Dành cho người không ăn ớt' },
                    { id: 'mild', name: 'Cay nhẹ (Chuẩn vị)', desc: 'Hơi tê tê đầu lưỡi, chuẩn vị GAO' },
                    { id: 'medium', name: 'Cay vừa', desc: 'Vị cay ấm nồng đậm đà' },
                    { id: 'hot', name: 'Cay nhiều 🔥', desc: 'Thách thức tín đồ ăn cay' },
                ],

                // Menu Categories
                categories: [
                    { id: 'all', name: 'Tất Cả', icon: '✨', count: 11 },
                    { id: 'rice', name: 'Cơm Gà', icon: '🍚', count: 4 },
                    { id: 'chicken', name: 'Gà Sốt', icon: '🍗', count: 3 },
                    { id: 'combo', name: 'Combo', icon: '👥', count: 3 },
                    { id: 'side', name: 'Ăn Kèm', icon: '🍟', count: 2 },
                    { id: 'drink', name: 'Đồ Uống', icon: '🥤', count: 2 },
                ],

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

                // Upsell suggestions inside cart
                upsellItems: [
                    { id: 'up-1', name: 'Coca Cola (Lon 320ml)', price: 12000, image: 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=300&q=80', icon: '🥤' },
                    { id: 'up-2', name: 'Khoai Tây Chiên Giòn', price: 20000, image: 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=300&q=80', icon: '🍟' },
                    { id: 'up-3', name: 'Trứng Ốp La', price: 10000, image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=300&q=80', icon: '🍳' }
                ],
                
                toast: {
                    show: false,
                    title: '',
                    message: '',
                    timeout: null
                },

                // List of 4 main signature sauces
                sauceList: [
                    {
                        id: 'korean_spicy',
                        name: 'Sốt Cay Hàn',
                        icon: '🌶️',
                        tag: '🌶️ Vị cay đặc trưng',
                        subtitle: 'Cay nhẹ, ngọt hậu, thơm nồng ớt Gochujang Hàn Quốc.',
                        shortDesc: 'Đậm đà cay',
                        description: 'Là sốt đặc trưng làm nên thương hiệu GAO. Gà giòn tan quyện cùng nước sốt sánh mịn óng ả, phủ đều từng thớ thịt.',
                        image: 'https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?auto=format&fit=crop&w=900&q=80',
                        price: 49000
                    },
                    {
                        id: 'honey_butter',
                        name: 'Sốt Mật Ong',
                        icon: '🍯',
                        tag: '🍯 Ngọt dịu thanh tao',
                        subtitle: 'Vị ngọt ngào từ mật ong rừng hòa quyện bơ béo thơm lừng.',
                        shortDesc: 'Ngọt dịu thơm bơ',
                        description: 'Sốt mật ong vàng óng ánh, thấm đẫm lớp vỏ gà chiên giòn tan, vị ngọt ngào dễ ăn phù hợp cho cả trẻ nhỏ.',
                        image: 'https://images.unsplash.com/photo-1527477321055-43615862e771?auto=format&fit=crop&w=900&q=80',
                        price: 49000
                    },
                    {
                        id: 'garlic_butter',
                        name: 'Sốt Bơ Tỏi',
                        icon: '🧄',
                        tag: '🧄 Thơm nức mũi',
                        subtitle: 'Hương tỏi phi vàng rụm kết hợp bơ thực vật béo ngậy.',
                        shortDesc: 'Béo ngậy thơm lừng',
                        description: 'Tỏi phi giòn rụm rắc đều trên từng miếng gà phủ sốt bơ bóng bẩy. Mùi thơm nức mũi kích thích trọn vẹn vị giác.',
                        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=900&q=80',
                        price: 49000
                    },
                    {
                        id: 'sweet_sour',
                        name: 'Sốt Chua Ngọt',
                        icon: '🥭',
                        tag: '🥭 Chua ngọt đậm đà',
                        subtitle: 'Vị chua ngọt hoa quả tươi mát kích thích vị giác.',
                        shortDesc: 'Chua ngọt thơm mát',
                        description: 'Lớp sốt chua ngọt bóng bẩy với sốt me dứa đậm đà, ăn nhiều không ngấy, hương vị tươi mát độc đáo.',
                        image: 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=900&q=80',
                        price: 49000
                    }
                ],

                // Full Menu Items Matching Screenshot 4 (Thực đơn đặt món)
                allMenuItems: [
                    {
                        id: 'dish-1',
                        name: 'Cơm Gà Sốt Cay',
                        category: 'rice',
                        tag: 'BEST SELLER',
                        rating: '4.9',
                        reviewCount: '384',
                        description: 'Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.',
                        price: 49000,
                        sauce: 'Sốt Cay Hàn',
                        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-2',
                        name: 'Cơm Gà Sốt Mật Ong',
                        category: 'rice',
                        tag: null,
                        rating: '4.8',
                        reviewCount: '256',
                        description: 'Gà giòn óng ả phủ sốt mật ong hoa rừng ngọt dịu, ăn cùng cơm nóng và dưa góp thanh mát.',
                        price: 49000,
                        sauce: 'Sốt Mật Ong',
                        image: 'https://images.unsplash.com/photo-1512058564366-18510be2db19?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-3',
                        name: 'Cơm Gà Bơ Tỏi',
                        category: 'rice',
                        tag: 'MỚI',
                        rating: '4.9',
                        reviewCount: '198',
                        description: 'Gà vàng ươm tẩm sốt bơ tỏi thơm nức, rắc tỏi phi giòn tan, vị béo ngậy cuốn hút khó cưỡng.',
                        price: 49000,
                        sauce: 'Sốt Bơ Tỏi',
                        image: 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-4',
                        name: 'Cơm Gà Sốt Chua Ngọt',
                        category: 'rice',
                        tag: null,
                        rating: '4.7',
                        reviewCount: '142',
                        description: 'Gà giòn tan quyện sốt chua ngọt bắt vị, thanh mát giải ngấy cho bữa trưa đầy năng lượng.',
                        price: 49000,
                        sauce: 'Sốt Chua Ngọt',
                        image: 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-5',
                        name: 'Gà Sốt Cay (Phần Lớn)',
                        category: 'chicken',
                        tag: null,
                        rating: '4.9',
                        reviewCount: '310',
                        description: 'Gà không xương giòn rụm đẫm sốt cay đỏ óng ả, rắc mè rang thơm bùi chuẩn vị Hàn Quốc.',
                        price: 45000,
                        sauce: 'Sốt Cay Hàn',
                        image: 'https://images.unsplash.com/photo-1587593810167-a84920ea0781?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-6',
                        name: 'Gà Sốt Mật Ong (Phần Lớn)',
                        category: 'chicken',
                        tag: null,
                        rating: '4.8',
                        reviewCount: '215',
                        description: 'Phần gà chiên giòn tẩm sốt mật ong thơm phức, lớp da bóng bẩy cuốn hút mọi lứa tuổi.',
                        price: 45000,
                        sauce: 'Sốt Mật Ong',
                        image: 'https://images.unsplash.com/photo-1527477321055-43615862e771?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-7',
                        name: 'Gà Sốt Bơ Tỏi (Phần Lớn)',
                        category: 'chicken',
                        tag: null,
                        rating: '4.8',
                        reviewCount: '188',
                        description: 'Gà giòn thơm nức mùi bơ tỏi phi, béo bùi ngập tràn từng miếng cắn.',
                        price: 45000,
                        sauce: 'Sốt Bơ Tỏi',
                        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-8',
                        name: 'Khoai Tây Chiên Giòn',
                        category: 'side',
                        tag: null,
                        rating: '4.7',
                        reviewCount: '175',
                        description: 'Khoai tây cắt thanh chiên vàng giòn rụm, lắc muối tiêu thơm ngon.',
                        price: 20000,
                        sauce: null,
                        image: 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-9',
                        name: 'Trứng Ốp La Lòng Đào',
                        category: 'side',
                        tag: null,
                        rating: '4.9',
                        reviewCount: '290',
                        description: 'Trứng gà tươi ốp la lòng đào béo ngậy, chảy tràn trên cơm nóng hấp dẫn.',
                        price: 10000,
                        sauce: null,
                        image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-10',
                        name: 'Coca Cola (Lon 320ml)',
                        category: 'drink',
                        tag: null,
                        rating: '5',
                        reviewCount: '420',
                        description: 'Coca Cola mát lạnh đã khát, uống kèm gà chiên giòn chuẩn vị.',
                        price: 12000,
                        sauce: null,
                        image: 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'dish-11',
                        name: 'Pepsi (Lon 320ml)',
                        category: 'drink',
                        tag: null,
                        rating: '5',
                        reviewCount: '310',
                        description: 'Pepsi ướp lạnh sảng khoái, giải ngấy tức thì sau từng miếng gà.',
                        price: 12000,
                        sauce: null,
                        image: 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'combo-1',
                        name: 'COMBO SOLO',
                        category: 'combo',
                        tag: 'TIẾT KIỆM',
                        rating: '4.9',
                        reviewCount: '150',
                        description: '1 Cơm gà sốt tuỳ chọn + 1 Nước ngọt có gas.',
                        price: 69000,
                        sauce: 'Sốt Cay Hàn',
                        image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'combo-2',
                        name: 'COMBO DUO',
                        category: 'combo',
                        tag: 'BÁN CHẠY',
                        rating: '5.0',
                        reviewCount: '280',
                        description: '2 Cơm gà sốt tuỳ chọn + 2 Nước ngọt + 1 Khoai tây lắc.',
                        price: 99000,
                        sauce: 'Sốt Cay Hàn',
                        image: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=700&q=80'
                    },
                    {
                        id: 'combo-3',
                        name: 'COMBO FULL',
                        category: 'combo',
                        tag: 'TIẾT KIỆM',
                        rating: '4.9',
                        reviewCount: '190',
                        description: '1 Mẹt gà sốt 8 miếng + 1 Gà popcorn + 1 Khoai chiên + 3 Nước ngọt.',
                        price: 149000,
                        sauce: 'Sốt Cay Hàn',
                        image: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=700&q=80'
                    }
                ],

                // Cart state with items exactly matching Screenshot 5 & 6 (313.000đ)
                cartItems: [
                    {
                        id: 'cart-1',
                        name: 'Cơm Gà Sốt Cay',
                        price: 49000,
                        quantity: 3,
                        sauce: 'Sốt Cay Hàn',
                        spiceLevel: 'Cay nhẹ (Chuẩn vị)',
                        toppings: [],
                        image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=300&q=80'
                    },
                    {
                        id: 'cart-2',
                        name: 'Coca Cola (Lon 320ml)',
                        price: 12000,
                        quantity: 1,
                        sauce: null,
                        spiceLevel: null,
                        toppings: [],
                        image: 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=300&q=80'
                    },
                    {
                        id: 'cart-3',
                        name: 'Khoai Tây Chiên Giòn',
                        price: 20000,
                        quantity: 2,
                        sauce: null,
                        spiceLevel: null,
                        toppings: [],
                        image: 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=300&q=80'
                    },
                    {
                        id: 'cart-4',
                        name: 'COMBO SOLO',
                        price: 69000,
                        quantity: 1,
                        sauce: null,
                        spiceLevel: null,
                        toppings: [],
                        image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=300&q=80'
                    },
                    {
                        id: 'cart-5',
                        name: 'Gà Sốt Mật Ong (Phần Lớn)',
                        price: 45000,
                        quantity: 1,
                        sauce: 'Sốt Mật Ong',
                        spiceLevel: null,
                        toppings: [],
                        image: 'https://images.unsplash.com/photo-1527477321055-43615862e771?auto=format&fit=crop&w=300&q=80'
                    }
                ],

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
                    return this.sauceList.find(s => s.id === this.selectedSauceId) || this.sauceList[0];
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
                    return this.allMenuItems.filter(i => ['dish-1', 'dish-2', 'dish-3', 'dish-5'].includes(i.id));
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

                // Open modal to customize dish before adding (For Hot items & all menu items)
                openCustomize(item) {
                    let defaultSauce = 'Sốt Cay Hàn';
                    if (item.sauce) {
                        defaultSauce = item.sauce;
                    } else if (item.name) {
                        if (item.name.includes('Mật Ong')) defaultSauce = 'Sốt Mật Ong';
                        else if (item.name.includes('Bơ Tỏi') || item.name.includes('Tỏi')) defaultSauce = 'Sốt Bơ Tỏi';
                        else if (item.name.includes('Chua Ngọt')) defaultSauce = 'Sốt Chua Ngọt';
                        else if (item.name.includes('Cay')) defaultSauce = 'Sốt Cay Hàn';
                    }

                    this.customizingItem = {
                        id: item.id || 'dish-' + Date.now(),
                        name: item.name || 'Cơm Gà Sốt Cay',
                        basePrice: item.price || 49000,
                        image: item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=700&q=80',
                        rating: item.rating ? (String(item.rating).includes('đánh giá') ? item.rating : `${item.rating} (${item.reviewCount || 384} đánh giá)`) : '4.9 (384 đánh giá)',
                        description: item.description || 'Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.',
                        quantity: 1,
                        selectedSauce: defaultSauce,
                        selectedSpiceLevel: 'Cay nhẹ (Chuẩn vị)',
                        selectedToppings: [],
                        note: ''
                    };
                    this.openCustomizeModal = true;
                },

                // Open modal when user chooses from the Sauce Section
                openCustomizeFromSauce(sauce) {
                    this.customizingItem = {
                        id: 'dish-sauce-' + sauce.id,
                        name: 'Cơm Gà ' + sauce.name,
                        basePrice: sauce.price || 49000,
                        image: sauce.image,
                        rating: '4.9 (384 đánh giá)',
                        description: sauce.description || 'Đùi gà chiên giòn rụm phủ đẫm sốt thơm ngon, ăn kèm cơm dẻo và dưa chua.',
                        quantity: 1,
                        selectedSauce: sauce.name,
                        selectedSpiceLevel: 'Cay nhẹ (Chuẩn vị)',
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

                // Submit Order logic
                submitOrder() {
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

                    // Generate order code
                    const orderCode = 'GAO-' + Math.floor(100000 + Math.random() * 900000);
                    this.orderSuccessData = {
                        orderCode: orderCode,
                        totalAmount: this.totalPrice
                    };

                    // Close checkout modal & Open success confirmation modal
                    this.openCheckoutModal = false;
                    this.openSuccessModal = true;

                    // Clear cart
                    this.cartItems = [];
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
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #faf6f0;
            color: #1a1a1a;
        }
        [x-cloak] { display: none !important; }
        
        .hero-glow {
            background: radial-gradient(circle at 60% 50%, rgba(254, 215, 170, 0.35) 0%, rgba(250, 246, 240, 0) 70%);
        }
        
        .red-glow {
            box-shadow: 0 10px 30px -5px rgba(209, 36, 19, 0.4);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #faf6f0;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2d8cb;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d12413;
        }
    </style>
</head>
<body x-data="gaoApp()" class="antialiased text-gray-800 bg-[#FAF6F0] selection:bg-red-500 selection:text-white">

    <!-- TOP NOTIFICATION BAR -->
    <div class="bg-gradient-to-r from-red-600 to-amber-600 text-white text-xs font-semibold py-1.5 px-4 text-center tracking-wide flex items-center justify-center gap-2">
        <span class="inline-block animate-pulse">🔥</span>
        <span>Ưu đãi hôm nay: Freeship bán kính 3km cho đơn hàng từ 100k!</span>
        <span class="hidden md:inline">• Hotline đặt món: <strong>0988.868.GAO</strong></span>
    </div>

    <!-- MAIN NAVBAR -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-orange-100/80 transition-all duration-300 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Brand Logo -->
            <a href="#" @click.prevent="switchView('home')" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-red-600 via-orange-600 to-amber-500 flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.87-3.13-7-7-7zm-1 18h2v2h-2v-2z"/>
                        <circle cx="12" cy="9" r="2.5" fill="#fff" opacity="0.3"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-2xl font-black tracking-tight text-gray-900 leading-none">GAO</span>
                        <span class="text-[10px] uppercase font-extrabold px-1.5 py-0.5 bg-red-100 text-red-700 rounded-sm">Hà Nội</span>
                    </div>
                    <span class="text-[11px] font-bold text-gray-400 tracking-wider block mt-0.5">GÀ SỐT & CƠM</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-[15px] font-semibold">
                <button 
                    @click="switchView('home')" 
                    class="py-1 transition-colors"
                    :class="currentView === 'home' ? 'text-red-600 border-b-2 border-red-600 font-bold' : 'text-gray-600 hover:text-red-600'"
                >Trang chủ</button>
                <a 
                    href="#sauces" 
                    @click="if(currentView !== 'home') { switchView('home'); setTimeout(() => { document.getElementById('sauces')?.scrollIntoView({behavior: 'smooth'}) }, 100); }" 
                    class="text-gray-600 hover:text-red-600 transition-colors py-1"
                >Vị Sốt</a>
                <a 
                    href="#popular" 
                    @click="if(currentView !== 'home') { switchView('home'); setTimeout(() => { document.getElementById('popular')?.scrollIntoView({behavior: 'smooth'}) }, 100); }" 
                    class="text-gray-600 hover:text-red-600 transition-colors py-1"
                >Món Hot</a>
                <a 
                    href="#combos" 
                    @click="if(currentView !== 'home') { switchView('home'); setTimeout(() => { document.getElementById('combos')?.scrollIntoView({behavior: 'smooth'}) }, 100); }" 
                    class="text-gray-600 hover:text-red-600 transition-colors py-1"
                >Combo</a>
                <button 
                    @click="switchView('menu')" 
                    class="py-1 transition-colors flex items-center gap-1 font-bold"
                    :class="currentView === 'menu' ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-red-600'"
                >
                    <span>Menu đầy đủ</span>
                </button>
            </nav>

            <!-- Right Controls (Location, Cart, Order Button) -->
            <div class="flex items-center gap-3">
                <!-- Location badge -->
                <div class="hidden lg:flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-gray-200 bg-gray-50/80 text-xs font-medium text-gray-700 shadow-xs">
                    <span class="text-red-500 text-sm">📍</span>
                    <span>Hà Nội (3–5km)</span>
                </div>

                <!-- Shopping Cart Icon with Badge -->
                <button 
                    @click="isCartOpen = true" 
                    type="button"
                    class="relative p-2.5 rounded-full border border-gray-200 text-gray-700 hover:border-red-400 hover:text-red-600 transition-all bg-white shadow-xs focus:outline-none"
                    aria-label="Giỏ hàng"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span 
                        x-text="totalItemsCount" 
                        class="absolute -top-1 -right-1 bg-red-600 text-white font-extrabold text-[11px] w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-xs animate-bounce"
                        x-show="totalItemsCount > 0"
                    >8</span>
                    <span 
                        x-show="totalItemsCount === 0" 
                        class="absolute -top-1 -right-1 bg-gray-400 text-white font-bold text-[10px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"
                    >0</span>
                </button>

                <!-- Order CTA Button -->
                <button 
                    @click="switchView('menu')" 
                    type="button"
                    class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white text-sm font-bold shadow-md red-glow transition-all duration-200 active:scale-95"
                >
                    <span class="text-base">🍗</span>
                    <span>Đặt món</span>
                </button>
            </div>
        </div>
    </header>

    <!-- VIEW 1: LANDING PAGE VIEW (currentView === 'home') -->
    <main x-show="currentView === 'home'">
        
        <!-- HERO SECTION -->
        <section id="hero" class="relative overflow-hidden py-12 lg:py-16 hero-glow border-b border-orange-100/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                    
                    <!-- Left Column: Copywriting & CTA -->
                    <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-50 border border-red-200/80 text-red-600 font-bold text-xs tracking-wider uppercase shadow-xs">
                            <span class="inline-block text-sm">✦</span>
                            <span>GÀ CHIÊN + SỐT ĐẬM VỊ</span>
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black tracking-tight text-gray-900 leading-[1.05]">
                            GÀ GIÒN.<br>
                            <span class="text-red-600 inline-block drop-shadow-xs">SỐT ĐẬM.</span>
                        </h1>

                        <p class="text-gray-600 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                            Gà nóng giòn, phủ sốt nguyên bản. Giao tận nơi tại Hà Nội trong 25–40 phút.
                        </p>

                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                            <button 
                                @click="switchView('menu')" 
                                class="px-8 py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm sm:text-base tracking-wide uppercase shadow-lg red-glow transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0"
                            >
                                ĐẶT MÓN NGAY
                            </button>
                            <button 
                                @click="switchView('menu')"
                                type="button"
                                class="px-7 py-4 rounded-full bg-white hover:bg-gray-50 text-gray-800 font-bold text-sm sm:text-base tracking-wide border border-gray-200 shadow-sm transition-all duration-200 hover:border-gray-300"
                            >
                                XEM MENU
                            </button>
                        </div>

                        <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-6 sm:gap-8 text-xs sm:text-sm font-bold text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="text-red-500 text-base">⚡</span>
                                <span>Giao 25–40p</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-amber-500 text-base">🔥</span>
                                <span>Luôn nóng giòn</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-yellow-500 text-base">⭐</span>
                                <span>Đánh giá 4.9/5</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Hero Visual Graphic -->
                    <div class="lg:col-span-6 relative flex justify-center">
                        <div class="relative w-full max-w-lg cursor-pointer group" @click="openCustomize(allMenuItems[0])">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-red-500/20 to-amber-400/20 rounded-full blur-2xl opacity-70"></div>
                            
                            <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white aspect-[4/3] bg-gray-900">
                                <img 
                                    src="https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=1000&q=85" 
                                    alt="Gà Giòn Sốt Đậm GAO" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                                <div class="absolute bottom-5 right-5 bg-white/95 backdrop-blur-md py-2 px-4 rounded-full shadow-lg border border-red-100 flex items-center gap-2">
                                    <span class="text-[11px] font-bold uppercase text-gray-500 tracking-wider">CHỈ TỪ</span>
                                    <span class="text-lg font-black text-red-600">49.000đ</span>
                                </div>
                            </div>

                            <div class="absolute -top-3 -left-3 bg-red-600 text-white font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow-md flex items-center gap-1.5 hover:scale-105 transition-transform">
                                <span>🔥</span>
                                <span>MÓN MỚI RA MẮT • ĐẶT NGAY</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- SECTION: CHỌN SỐT CỦA BẠN (INTERACTIVE SAUCE SELECTOR) -->
        <section id="sauces" class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center space-y-2 mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-gray-900 uppercase">
                        CHỌN SỐT CỦA BẠN
                    </h2>
                    <p class="text-gray-500 text-base font-semibold italic">
                        Gà giòn. Đậm chuẩn vị
                    </p>
                </div>

                <div class="bg-[#FAF6F0] rounded-3xl p-6 sm:p-10 border border-orange-100 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        
                        <div class="lg:col-span-6">
                            <div class="relative rounded-2xl overflow-hidden aspect-[4/3] shadow-lg border-2 border-white bg-gray-900">
                                <img 
                                    :src="currentSauce.image" 
                                    :alt="currentSauce.name" 
                                    class="w-full h-full object-cover transition-opacity duration-300"
                                />
                                <div class="absolute top-4 left-4 bg-black/70 backdrop-blur-md text-white text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                                    <span x-text="currentSauce.icon">🌶️</span>
                                    <span x-text="currentSauce.name">Sốt Cay Hàn</span>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-6 space-y-6">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                    🔥 Bán chạy nhất
                                </span>
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold" x-text="currentSauce.tag">
                                    🌶️ Vị cay đặc trưng
                                </span>
                            </div>

                            <div>
                                <h3 class="text-3xl sm:text-4xl font-black text-red-600 tracking-tight" x-text="currentSauce.name">
                                    Sốt Cay Hàn
                                </h3>
                                <p class="text-base font-bold text-gray-700 mt-1" x-text="currentSauce.subtitle">
                                    Cay nhẹ, ngọt hậu, thơm nồng ớt Gochujang Hàn Quốc.
                                </p>
                            </div>

                            <p class="text-gray-600 text-sm sm:text-base leading-relaxed" x-text="currentSauce.description">
                                Là sốt đặc trưng làm nên thương hiệu GAO. Gà giòn tan quyện cùng nước sốt sánh mịn óng ả, phủ đều từng thớ thịt.
                            </p>

                            <!-- Sauce Switcher Grid -->
                            <div class="grid grid-cols-2 gap-3 pt-2">
                                <template x-for="(sauce, index) in sauceList" :key="sauce.id">
                                    <button 
                                        @click="selectSauce(sauce)" 
                                        type="button"
                                        class="p-3.5 rounded-xl text-left border-2 transition-all flex items-start gap-2.5"
                                        :class="selectedSauceId === sauce.id ? 'border-red-600 bg-red-50/80 shadow-xs' : 'border-gray-200 bg-white hover:border-gray-300'"
                                    >
                                        <span class="text-xl" x-text="sauce.icon">🌶️</span>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900" x-text="sauce.name"></div>
                                            <div class="text-[11px] font-semibold text-gray-500" x-text="sauce.shortDesc"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Action Button: Opens Customization Modal with current selected sauce -->
                            <div class="pt-2">
                                <button 
                                    @click="openCustomizeFromSauce(currentSauce)" 
                                    type="button"
                                    class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-md red-glow transition-all active:scale-95 flex items-center justify-center gap-2"
                                >
                                    <span>CHỌN VỊ NÀY</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- SECTION: MÓN ĐƯỢC GỌI NHIỀU (POPULAR ITEMS) -->
        <section id="popular" class="py-16 lg:py-20 bg-[#FAF6F0] border-b border-orange-100/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center space-y-2 mb-12">
                    <div class="inline-flex items-center gap-2 text-red-600 font-extrabold text-sm uppercase tracking-widest">
                        <span>🔥</span>
                        <span>MÓN ĐƯỢC GỌI NHIỀU</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight">
                        Thực Đơn Đậm Vị Được Yêu Thích Nhất
                    </h2>
                    <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
                        Những món gà sốt và cơm gà được đặt nhiều nhất mỗi ngày tại GAO
                    </p>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="item in popularItems" :key="item.id">
                        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                            
                            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(item)">
                                <img 
                                    :src="item.image" 
                                    :alt="item.name" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div class="absolute top-3 left-3" x-show="item.tag">
                                    <span 
                                        class="px-2.5 py-1 rounded-md text-[11px] font-extrabold uppercase text-white shadow-xs"
                                        :class="item.tag === 'BEST SELLER' || item.tag === 'BÁN CHẠY' ? 'bg-red-600' : 'bg-amber-500'"
                                        x-text="item.tag"
                                    >BÁN CHẠY</span>
                                </div>
                                <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur-xs px-2 py-0.5 rounded-full text-xs font-bold text-gray-800 flex items-center gap-1 shadow-xs">
                                    <span class="text-amber-400">⭐</span>
                                    <span x-text="item.rating">4.9</span>
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-2 cursor-pointer" @click="openCustomize(item)">
                                    <h3 class="font-extrabold text-base text-gray-900 group-hover:text-red-600 transition-colors" x-text="item.name">
                                        Cơm Gà Sốt Cay
                                    </h3>
                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-2" x-text="item.description">
                                        Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-lg font-black text-red-600" x-text="formatCurrency(item.price)">49.000đ</span>
                                    </div>
                                    <button 
                                        @click="openCustomize(item)" 
                                        type="button"
                                        class="px-4 py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs tracking-wide border border-red-200 transition-all duration-200 active:scale-95 flex items-center gap-1"
                                    >
                                        <span>+ Thêm</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                <div class="mt-12 text-center">
                    <button 
                        @click="switchView('menu')"
                        type="button"
                        class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-extrabold text-sm tracking-wide transition-all duration-200 shadow-xs"
                    >
                        <span>XEM TOÀN BỘ MENU</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

            </div>
        </section>

        <!-- SECTION: ĂN COMBO, LỜI HƠN -->
        <section id="combos" class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center space-y-2 mb-14">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight uppercase">
                        ĂN COMBO, LỜI HƠN
                    </h2>
                    <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
                        Tiết kiệm tới 55.000đ khi đi theo nhóm, ăn no nê cùng bạn bè & người thân.
                    </p>
                </div>

                <!-- Combo Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
                    
                    <!-- Combo 1: Solo -->
                    <div class="bg-[#FAF6F0] rounded-3xl overflow-hidden border border-gray-200 shadow-sm flex flex-col justify-between transition-all hover:shadow-lg">
                        <div>
                            <div class="relative aspect-[16/10] overflow-hidden bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=700&q=80" alt="Combo Solo" class="w-full h-full object-cover">
                                <span class="absolute top-3 right-3 bg-amber-500 text-white text-[11px] font-extrabold px-2.5 py-1 rounded-md uppercase">TIẾT KIỆM</span>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase">COMBO SOLO</h3>
                                    <p class="text-xs font-bold text-gray-500">Dành cho 1 người</p>
                                </div>
                                <ul class="space-y-2 text-xs font-semibold text-gray-600">
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>1 Cơm gà sốt tuỳ chọn (49k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>1 Lon nước ngọt có gas (15k)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-gray-200/60 mt-4">
                            <div>
                                <span class="text-xs text-gray-400 line-through block">64.000đ</span>
                                <span class="text-xl font-black text-red-600">49.000đ</span>
                            </div>
                            <button 
                                @click="addToCartDirect({id: 'combo-1', name: 'COMBO SOLO', price: 69000, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=700&q=80'})"
                                type="button" 
                                class="px-5 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs tracking-wide transition-all shadow-xs active:scale-95"
                            >
                                Chọn combo
                            </button>
                        </div>
                    </div>

                    <!-- Combo 2: Duo -->
                    <div class="bg-white rounded-3xl overflow-hidden border-2 border-red-600 shadow-xl flex flex-col justify-between transform lg:-translate-y-2 relative">
                        <div class="bg-red-600 text-white text-center py-2 text-xs font-black uppercase tracking-wider">
                            🔥 BÁN CHẠY NHẤT • TIẾT KIỆM 40K
                        </div>

                        <div>
                            <div class="relative aspect-[16/10] overflow-hidden bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=700&q=80" alt="Combo Duo" class="w-full h-full object-cover">
                                <span class="absolute top-3 right-3 bg-amber-500 text-white text-[11px] font-extrabold px-2.5 py-1 rounded-md uppercase">TIẾT KIỆM</span>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase">COMBO DUO</h3>
                                    <p class="text-xs font-bold text-red-600">Ăn no gà — 2 người</p>
                                </div>
                                <ul class="space-y-2.5 text-xs font-semibold text-gray-700">
                                    <li class="flex items-center gap-2">
                                        <span class="text-red-600 font-bold">✓</span>
                                        <span>2 Cơm gà sốt tuỳ chọn (98k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-red-600 font-bold">✓</span>
                                        <span>2 Lon nước ngọt có gas (30k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-red-600 font-bold">✓</span>
                                        <span>1 Phần khoai tây lắc (25k)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-gray-100 mt-4">
                            <div>
                                <span class="text-xs text-gray-400 line-through block">149.000đ</span>
                                <span class="text-2xl font-black text-red-600">99.000đ</span>
                            </div>
                            <button 
                                @click="addToCartDirect({id: 'combo-2', name: 'COMBO DUO', price: 99000, image: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=700&q=80'})"
                                type="button" 
                                class="px-6 py-2.5 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-xs tracking-wide transition-all shadow-md red-glow active:scale-95"
                            >
                                Chọn combo
                            </button>
                        </div>
                    </div>

                    <!-- Combo 3: Full Party -->
                    <div class="bg-[#FAF6F0] rounded-3xl overflow-hidden border border-gray-200 shadow-sm flex flex-col justify-between transition-all hover:shadow-lg">
                        <div>
                            <div class="relative aspect-[16/10] overflow-hidden bg-gray-200">
                                <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=700&q=80" alt="Combo Full" class="w-full h-full object-cover">
                                <span class="absolute top-3 right-3 bg-amber-500 text-white text-[11px] font-extrabold px-2.5 py-1 rounded-md uppercase">TIẾT KIỆM</span>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase">COMBO FULL</h3>
                                    <p class="text-xs font-bold text-gray-500">Party ngập tràn — 3-4 người</p>
                                </div>
                                <ul class="space-y-2 text-xs font-semibold text-gray-600">
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>1 Mẹt gà sốt 8 miếng (149k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>1 Phần gà popcorn (39k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>1 Khoai tây chiên (29k)</span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="text-emerald-600 font-bold">✓</span>
                                        <span>3 Nước ngọt (45k)</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-gray-200/60 mt-4">
                            <div>
                                <span class="text-xs text-gray-400 line-through block">262.000đ</span>
                                <span class="text-xl font-black text-red-600">149.000đ</span>
                            </div>
                            <button 
                                @click="addToCartDirect({id: 'combo-3', name: 'COMBO FULL', price: 149000, image: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=700&q=80'})"
                                type="button" 
                                class="px-5 py-2 rounded-full bg-red-600 hover:bg-red-700 text-white font-bold text-xs tracking-wide transition-all shadow-xs active:scale-95"
                            >
                                Chọn combo
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- SECTION: BENEFIT BAR -->
        <section class="py-10 bg-[#FAF6F0]">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200/70 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-gray-100">
                    
                    <div class="flex items-center gap-4 px-4 pt-4 md:pt-0">
                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-2xl shrink-0 shadow-xs">
                            ⏱️
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base">25–40 phút</h4>
                            <p class="text-xs text-gray-500 font-medium">Giao nhanh nóng hổi nội thành Hà Nội</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 px-4 pt-4 md:pt-0">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl shrink-0 shadow-xs">
                            🔥
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base">Làm mới mỗi ngày</h4>
                            <p class="text-xs text-gray-500 font-medium">Gà tươi chiên giòn, sốt nấu mới mỗi ngày</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 px-4 pt-4 md:pt-0">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0 shadow-xs">
                            🛵
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-base">Freeship 3km</h4>
                            <p class="text-xs text-gray-500 font-medium">Áp dụng cho đơn từ 100k trở lên</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- SECTION: KHÁCH ĂN NÓI GÌ? (TESTIMONIALS) -->
        <section class="py-16 lg:py-20 bg-white border-b border-orange-100/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center space-y-2 mb-12">
                    <h2 class="text-3xl sm:text-4xl font-black text-gray-900 tracking-tight uppercase">
                        KHÁCH ĂN NÓI GÌ?
                    </h2>
                    <p class="text-gray-500 text-sm sm:text-base font-medium">
                        Hơn 10.000+ bữa ăn ngon đã được giao đến tay khách hàng tại Hà Nội
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="bg-[#FAF6F0] rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-amber-400 text-sm tracking-wider">★★★★★</div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span>✔</span> ĐÃ MUA TẠI GAO
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-700 leading-relaxed italic">
                                "Gà giòn rụm mà sốt cay ngọt đỉnh chóp. Cơm dẻo thơm, dưa chua vừa vị. 10/10 điểm cho bữa trưa văn phòng!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-200/60">
                            <div class="w-10 h-10 rounded-full bg-rose-200 text-rose-800 font-black text-xs flex items-center justify-center">
                                MA
                            </div>
                            <div>
                                <div class="font-bold text-xs text-gray-900">Minh Anh</div>
                                <div class="text-[11px] text-gray-500">Nhân viên văn phòng Cầu Giấy</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#FAF6F0] rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-amber-400 text-sm tracking-wider">★★★★★</div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span>✔</span> ĐÃ MUA TẠI GAO
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-700 leading-relaxed italic">
                                "Ship đến gà vẫn giòn và nóng hổi. Hộp đóng gói sạch đẹp, sốt bơ tỏi thơm ngất ngây. Sẽ ủng hộ dài dài!"
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-200/60">
                            <div class="w-10 h-10 rounded-full bg-amber-200 text-amber-800 font-black text-xs flex items-center justify-center">
                                ĐT
                            </div>
                            <div>
                                <div class="font-bold text-xs text-gray-900">Đức Tuấn</div>
                                <div class="text-[11px] text-gray-500">Sinh viên ĐH Quốc Gia Hà Nội</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-[#FAF6F0] rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between space-y-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="text-amber-400 text-sm tracking-wider">★★★★★</div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <span>✔</span> ĐÃ MUA TẠI GAO
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-700 leading-relaxed italic">
                                "Ăn combo duo với bạn gái ăn không hết luôn, quá nhiều gà mà giá siêu hạt dẻ. Sốt cay Hàn ăn dính lắm."
                            </p>
                        </div>
                        <div class="flex items-center gap-3 pt-2 border-t border-gray-200/60">
                            <div class="w-10 h-10 rounded-full bg-purple-200 text-purple-800 font-black text-xs flex items-center justify-center">
                                PL
                            </div>
                            <div>
                                <div class="font-bold text-xs text-gray-900">Phương Linh</div>
                                <div class="text-[11px] text-gray-500">Kế toán tại Ba Đình</div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>

    </main>

    <!-- VIEW 2: THỰC ĐƠN ĐẶT MÓN (currentView === 'menu' - EXACT MATCH SCREENSHOT 4) -->
    <main x-show="currentView === 'menu'" class="py-8 sm:py-12" id="menu-grid" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Back Button -->
            <div class="mb-6">
                <button 
                    @click="switchView('home')" 
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-50 hover:bg-red-100 text-red-600 text-xs font-extrabold border border-red-200 transition-colors"
                >
                    <span>←</span>
                    <span>Về trang chủ</span>
                </button>
            </div>

            <!-- Page Title -->
            <div class="text-center space-y-2 mb-8">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 tracking-tight uppercase">
                    THỰC ĐƠN ĐẶT MÓN
                </h1>
                <p class="text-gray-500 text-sm sm:text-base font-medium max-w-xl mx-auto">
                    Chọn món yêu thích, tuỳ chỉnh sốt & độ cay theo sở thích của bạn.
                </p>
            </div>

            <!-- Category Tabs Pill Filter -->
            <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-6">
                <template x-for="cat in categories" :key="cat.id">
                    <button 
                        @click="activeCategory = cat.id"
                        type="button"
                        class="px-4 sm:px-5 py-2.5 rounded-full text-xs sm:text-sm font-black transition-all flex items-center gap-1.5 shadow-xs"
                        :class="activeCategory === cat.id ? 'bg-red-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'"
                    >
                        <span x-text="cat.icon">✨</span>
                        <span x-text="cat.name">Tất Cả</span>
                        <span 
                            class="text-[11px] px-1.5 py-0.2 rounded-full"
                            :class="activeCategory === cat.id ? 'bg-red-700/80 text-white' : 'bg-gray-100 text-gray-500'"
                            x-text="cat.count"
                        >11</span>
                    </button>
                </template>
            </div>

            <!-- Search Bar -->
            <div class="max-w-md mx-auto mb-10">
                <div class="relative">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="🔍  Tìm tên món ăn..."
                        class="w-full px-5 py-3 rounded-full border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 shadow-xs placeholder:text-gray-400"
                    >
                    <button 
                        x-show="searchQuery" 
                        @click="searchQuery = ''"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs font-bold"
                    >✕</button>
                </div>
            </div>

            <!-- Menu Grid: 4 Columns on Desktop matching Screenshot -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <template x-for="dish in filteredMenuItems" :key="dish.id">
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                        
                        <!-- Top Thumbnail with Tag & Rating -->
                        <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 cursor-pointer" @click="openCustomize(dish)">
                            <img 
                                :src="dish.image" 
                                :alt="dish.name" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <!-- Tag / Badge -->
                            <div class="absolute top-3 left-3" x-show="dish.tag">
                                <span 
                                    class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase text-white shadow-xs"
                                    :class="dish.tag === 'BEST SELLER' ? 'bg-red-600' : (dish.tag === 'MỚI' ? 'bg-amber-500' : 'bg-emerald-600')"
                                    x-text="dish.tag"
                                >BEST SELLER</span>
                            </div>

                            <!-- Rating Star Badge -->
                            <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur-xs px-2.5 py-0.5 rounded-full text-xs font-bold text-gray-800 flex items-center gap-1 shadow-xs border border-gray-100">
                                <span class="text-amber-400">⭐</span>
                                <span x-text="dish.rating">4.9</span>
                            </div>
                        </div>

                        <!-- Card Content Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-1.5 cursor-pointer" @click="openCustomize(dish)">
                                <h3 class="font-black text-base text-gray-900 group-hover:text-red-600 transition-colors" x-text="dish.name">
                                    Cơm Gà Sốt Cay
                                </h3>
                                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2" x-text="dish.description">
                                    Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                                </p>
                            </div>

                            <!-- Card Footer: Price & Add Button -->
                            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                                <span class="text-base sm:text-lg font-black text-red-600" x-text="formatCurrency(dish.price)">
                                    49.000đ
                                </span>
                                <button 
                                    @click="openCustomize(dish)" 
                                    type="button"
                                    class="px-4 py-1.5 rounded-full bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs tracking-wide border border-red-200 transition-all duration-200 active:scale-95 flex items-center gap-1"
                                >
                                    <span>+ Thêm</span>
                                </button>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- Empty Search State -->
            <div x-show="filteredMenuItems.length === 0" class="text-center py-16 bg-white rounded-3xl p-8 border border-gray-200 max-w-md mx-auto">
                <div class="text-4xl mb-3">🔍</div>
                <h3 class="text-base font-bold text-gray-800">Không tìm thấy món ăn phù hợp</h3>
                <p class="text-xs text-gray-500 mt-1">Vui lòng thử tìm với từ khoá khác hoặc chọn lại danh mục.</p>
                <button 
                    @click="searchQuery = ''; activeCategory = 'all'" 
                    class="mt-4 px-6 py-2 rounded-full bg-red-600 text-white font-bold text-xs"
                >Xem tất cả món</button>
            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-[#141416] text-white pt-16 pb-28 md:pb-16 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
                
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center text-white shadow-md">
                            <span class="text-lg">🍗</span>
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight text-white block leading-none">GAO</span>
                            <span class="text-[10px] font-bold text-gray-400 tracking-wider">GÀ SỐT & CƠM</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed pr-4">
                        Thương hiệu Gà Sốt & Cơm chuẩn vị tại Hà Nội. Gà giòn rụm, đẫm sốt đậm đà, phục vụ nóng hổi tận tay khách hàng trong bán kính 3–5km.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">FB</a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">IG</a>
                        <a href="#" class="w-8 h-8 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center text-xs font-bold text-white transition-colors">TT</a>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-3">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wider">Thực Đơn</h5>
                    <ul class="space-y-2 text-xs text-gray-400 font-medium">
                        <li><a href="#" @click.prevent="switchView('menu'); activeCategory = 'rice'" class="hover:text-red-400 transition-colors">Cơm Gà Sốt</a></li>
                        <li><a href="#" @click.prevent="switchView('menu'); activeCategory = 'chicken'" class="hover:text-red-400 transition-colors">Gà Sốt Giòn</a></li>
                        <li><a href="#" @click.prevent="switchView('menu'); activeCategory = 'combo'" class="hover:text-red-400 transition-colors">Combo Tiết Kiệm</a></li>
                        <li><a href="#" @click.prevent="switchView('menu'); activeCategory = 'side'" class="hover:text-red-400 transition-colors">Món Ăn Kèm</a></li>
                        <li><a href="#" @click.prevent="switchView('menu'); activeCategory = 'drink'" class="hover:text-red-400 transition-colors">Đồ Uống Lạnh</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-3 space-y-3">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wider">Hỗ Trợ & Chính Sách</h5>
                    <ul class="space-y-2 text-xs text-gray-400 font-medium">
                        <li><a href="#" class="hover:text-red-400 transition-colors">Theo dõi đơn hàng</a></li>
                        <li><a href="#" class="hover:text-red-400 transition-colors">4 Loại Sốt Đặc Trưng</a></li>
                        <li><a href="#" class="hover:text-red-400 transition-colors">Chính sách Free Ship</a></li>
                        <li><a href="#" class="hover:text-red-400 transition-colors">Khu vực giao hàng</a></li>
                    </ul>
                </div>

                <div class="lg:col-span-3 space-y-3">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wider">Thông Tin Liên Hệ</h5>
                    <ul class="space-y-2.5 text-xs text-gray-400 font-medium">
                        <li class="flex items-start gap-2">
                            <span class="text-red-500">📍</span>
                            <span>Hà Nội: Đống Đa, Cầu Giấy, Hoàn Kiếm, Hai Bà Trưng, Ba Đình, Thanh Xuân.</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-red-500">📞</span>
                            <span>Hotline: <strong class="text-white">0988.868.GAO</strong></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-red-500">⏰</span>
                            <span>Giờ nhận đơn: 09:30 – 22:00 hàng ngày</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="mt-12 pt-8 border-t border-gray-800/80 text-center text-xs text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span>© 2026 GAO - Gà Sốt & Cơm Hà Nội. All rights reserved.</span>
                <span>Modern Vietnamese Food Delivery</span>
            </div>
        </div>
    </footer>

    <!-- STICKY BOTTOM ACTION BAR (FOR MOBILE & INSTANT ORDER) -->
    <div class="fixed bottom-0 left-0 right-0 z-30 p-3 bg-white/95 backdrop-blur-md border-t border-orange-200/80 shadow-2xl md:hidden">
        <div class="flex items-center gap-3">
            <button 
                @click="isCartOpen = true" 
                type="button"
                class="relative px-4 py-3 rounded-full border border-gray-300 text-gray-700 bg-white font-bold text-xs flex items-center gap-2"
            >
                <span>🛒</span>
                <span x-text="totalItemsCount > 0 ? totalItemsCount + ' món' : 'Giỏ'"></span>
            </button>
            <button 
                @click="switchView('menu')" 
                type="button"
                class="flex-1 py-3 px-6 rounded-full bg-gradient-to-r from-red-600 to-red-500 text-white font-extrabold text-sm tracking-wide shadow-lg red-glow text-center flex items-center justify-center gap-2 active:scale-95"
            >
                <span>🍗 ĐẶT MÓN NGAY</span>
                <span x-show="totalPrice > 0" x-text="'(' + formatCurrency(totalPrice) + ')'"></span>
            </button>
        </div>
    </div>

    <!-- MODAL 1: TUỲ CHỈNH MÓN ĂN (MATCHING SCREENSHOT 1 & 2) -->
    <div 
        x-show="openCustomizeModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-customize-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div 
                x-show="openCustomizeModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
                @click="openCustomizeModal = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content Box -->
            <div 
                x-show="openCustomizeModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full max-h-[90vh] flex flex-col"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-black text-gray-900" id="modal-customize-title">
                        Tuỳ chỉnh món ăn
                    </h3>
                    <button 
                        @click="openCustomizeModal = false" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="px-6 py-5 overflow-y-auto space-y-6 flex-1">
                    
                    <!-- Dish Header Summary -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100 shadow-xs">
                            <img :src="customizingItem.image" :alt="customizingItem.name" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 space-y-1">
                            <h4 class="text-xl font-black text-gray-900 leading-tight" x-text="customizingItem.name">
                                Cơm Gà Sốt Cay
                            </h4>
                            <div class="flex items-center gap-1 text-xs font-bold text-gray-500">
                                <span class="text-amber-400">⭐</span>
                                <span x-text="customizingItem.rating">4.9 (384 đánh giá)</span>
                            </div>
                            <div class="text-lg font-black text-red-600" x-text="formatCurrency(customizingItem.basePrice)">
                                49.000đ
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed pt-0.5 line-clamp-2" x-text="customizingItem.description">
                                Đùi gà chiên giòn rụm phủ đẫm sốt cay ngọt Hàn Quốc, ăn kèm cơm dẻo và dưa chua.
                            </p>
                        </div>
                    </div>

                    <!-- 1. Chọn loại sốt (Bắt buộc) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-black text-gray-900">1. Chọn loại sốt</h5>
                            <span class="text-[11px] font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full">Bắt buộc</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-for="sauce in sauceList" :key="sauce.id">
                                <button 
                                    @click="customizingItem.selectedSauce = sauce.name"
                                    type="button"
                                    class="p-3 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                    :class="customizingItem.selectedSauce === sauce.name ? 'border-red-500 bg-red-50/70 text-gray-900 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300 text-gray-700'"
                                >
                                    <span class="text-lg" x-text="sauce.icon">🌶️</span>
                                    <span class="text-xs font-black" x-text="sauce.name">Sốt Cay Hàn</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- 2. Chọn độ cay (Tuỳ chọn) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-black text-gray-900">2. Chọn độ cay</h5>
                            <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <template x-for="spice in spiceLevels" :key="spice.id">
                                <button 
                                    @click="customizingItem.selectedSpiceLevel = spice.name"
                                    type="button"
                                    class="p-3 rounded-2xl border-2 text-left transition-all"
                                    :class="customizingItem.selectedSpiceLevel === spice.name ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                                >
                                    <div class="text-xs font-black text-gray-900" x-text="spice.name">Cay nhẹ (Chuẩn vị)</div>
                                    <div class="text-[10px] text-gray-500 font-semibold mt-0.5 leading-tight" x-text="spice.desc">Hơi tê tê đầu lưỡi, chuẩn vị GAO</div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- 3. Thêm Topping / Ăn kèm (Tuỳ chọn) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-black text-gray-900">3. Thêm Topping / Ăn kèm</h5>
                            <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-full">Tuỳ chọn</span>
                        </div>
                        <div class="space-y-2">
                            <template x-for="top in availableToppings" :key="top.id">
                                <label 
                                    class="flex items-center justify-between p-3 rounded-2xl border-2 cursor-pointer transition-all"
                                    :class="customizingItem.selectedToppings.includes(top.id) ? 'border-red-400 bg-red-50/40 shadow-xs' : 'border-gray-200/70 bg-white hover:border-gray-300'"
                                >
                                    <div class="flex items-center gap-3">
                                        <input 
                                            type="checkbox" 
                                            :checked="customizingItem.selectedToppings.includes(top.id)"
                                            @change="toggleTopping(top.id)"
                                            class="w-4 h-4 text-red-600 rounded-md border-gray-300 focus:ring-red-500"
                                        >
                                        <span class="text-base" x-text="top.icon">🍳</span>
                                        <span class="text-xs font-bold text-gray-900" x-text="top.name">Trứng Ốp La Lòng Đào</span>
                                    </div>
                                    <span class="text-xs font-black text-red-600" x-text="'+' + formatCurrency(top.price)">+10.000đ</span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Ghi chú cho quán (Không bắt buộc) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h5 class="text-sm font-black text-gray-900">Ghi chú cho quán</h5>
                            <span class="text-[11px] font-bold text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full">Không bắt buộc</span>
                        </div>
                        <textarea 
                            x-model="customizingItem.note"
                            rows="2"
                            placeholder="Ví dụ: Cơm nhiều, ít tương ớt, để sốt riêng..."
                            class="w-full p-3 text-xs border border-gray-200 rounded-2xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                        ></textarea>
                    </div>

                </div>

                <!-- Modal Bottom Bar (Counter + Add to Cart Button) -->
                <div class="p-4 sm:p-5 bg-white border-t border-gray-100 flex items-center gap-3 shrink-0">
                    <!-- Quantity Controls -->
                    <div class="flex items-center gap-2 px-3 py-2 rounded-full border border-gray-200 bg-gray-50/80">
                        <button 
                            @click="if (customizingItem.quantity > 1) customizingItem.quantity--" 
                            class="w-6 h-6 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700"
                        >-</button>
                        <span class="font-black text-sm w-5 text-center text-gray-900" x-text="customizingItem.quantity">1</span>
                        <button 
                            @click="customizingItem.quantity++" 
                            class="w-6 h-6 rounded-full bg-white border border-gray-200 font-bold text-xs flex items-center justify-center hover:bg-gray-100 text-gray-700"
                        >+</button>
                    </div>

                    <!-- Add To Cart Button (Directly switches to Cart Drawer!) -->
                    <button 
                        @click="confirmAddToCart()" 
                        type="button" 
                        class="flex-1 py-3.5 px-6 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-extrabold text-sm tracking-wide shadow-lg red-glow transition-all active:scale-95 flex items-center justify-center gap-2"
                    >
                        <span>THÊM VÀO GIỎ</span>
                        <span>•</span>
                        <span x-text="formatCurrency(totalCustomizedPrice)">49.000đ</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- DRAWER: GIỎ HÀNG CỦA BẠN (MATCHING SCREENSHOT 3 EXACTLY) -->
    <div 
        x-show="isCartOpen" 
        class="fixed inset-0 z-50 overflow-hidden" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="isCartOpen"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
            @click="isCartOpen = false"
        ></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-6 sm:pl-10">
            <div 
                x-show="isCartOpen"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-[#FAF6F0] shadow-2xl flex flex-col"
            >
                <!-- Drawer Header -->
                <div class="p-5 bg-white border-b border-gray-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🛒</span>
                        <h2 class="text-lg font-black text-gray-900 tracking-tight" id="slide-over-title">
                            Giỏ Hàng Của Bạn
                        </h2>
                    </div>
                    <button 
                        @click="isCartOpen = false" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Free Ship Notification Bar -->
                <div class="bg-white px-5 py-3 border-b border-orange-100/70 shrink-0">
                    <div class="flex items-center gap-2 text-xs font-black text-gray-800">
                        <span>🎉</span>
                        <span>Bạn đã được <strong>FREE SHIP 3KM!</strong></span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-red-600 to-amber-500 rounded-full w-full"></div>
                    </div>
                </div>

                <!-- Drawer Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4">
                    
                    <!-- Empty State -->
                    <div x-show="cartItems.length === 0" class="text-center py-16 space-y-4 bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
                        <div class="w-20 h-20 mx-auto rounded-full bg-red-50 flex items-center justify-center text-4xl">
                            🍗
                        </div>
                        <h3 class="text-base font-bold text-gray-800">Chưa có món nào trong giỏ</h3>
                        <p class="text-xs text-gray-500 max-w-xs mx-auto">Hãy chọn ngay các món gà giòn phủ sốt thơm ngon để thưởng thức nhé!</p>
                        <button 
                            @click="isCartOpen = false; switchView('menu')" 
                            class="px-6 py-2.5 rounded-full bg-red-600 text-white font-bold text-xs tracking-wide shadow-md"
                        >
                            Xem thực đơn ngay
                        </button>
                    </div>

                    <!-- Cart Item Cards -->
                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="p-4 rounded-2xl border border-gray-200/70 bg-white shadow-xs space-y-3 relative">
                            
                            <!-- Delete Button (Top-Right X) -->
                            <button 
                                @click="removeItem(index)" 
                                class="absolute top-3.5 right-3.5 w-6 h-6 rounded-full bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-600 flex items-center justify-center transition-colors"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>

                            <div class="flex items-start gap-3.5 pr-6">
                                <!-- Food Thumbnail -->
                                <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-100">
                                    <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                </div>

                                <!-- Info & Option Badges -->
                                <div class="flex-1 min-w-0 space-y-1">
                                    <h4 class="font-black text-sm text-gray-900 leading-tight truncate" x-text="item.name">
                                        Cơm Gà Sốt Cay
                                    </h4>

                                    <!-- Option Tags (Sauce, Spice level, Toppings) -->
                                    <div class="flex flex-wrap gap-1.5 pt-0.5" x-show="item.sauce || item.spiceLevel">
                                        <span x-show="item.sauce" class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-md" x-text="'🌶️ ' + item.sauce">
                                            🌶️ Sốt Cay Hàn
                                        </span>
                                        <span x-show="item.spiceLevel" class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-100 px-2 py-0.5 rounded-md" x-text="item.spiceLevel">
                                            Cay nhẹ (Chuẩn vị)
                                        </span>
                                    </div>

                                    <!-- Toppings tag list -->
                                    <div class="text-[11px] text-gray-500 font-semibold" x-show="item.toppings && item.toppings.length > 0">
                                        + <span x-text="item.toppings.join(', ')"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Row: Total Item Price & Quantity Selector -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="text-base font-black text-red-600" x-text="formatCurrency(item.price * item.quantity)">
                                    147.000đ
                                </div>
                                <div class="flex items-center gap-2.5 px-3 py-1 rounded-full border border-gray-200 bg-gray-50">
                                    <button 
                                        @click="decrementItem(index)" 
                                        class="text-gray-500 hover:text-gray-900 font-bold text-xs w-4 text-center"
                                    >-</button>
                                    <span class="font-black text-xs w-4 text-center text-gray-900" x-text="item.quantity">3</span>
                                    <button 
                                        @click="incrementItem(index)" 
                                        class="text-gray-500 hover:text-gray-900 font-bold text-xs w-4 text-center"
                                    >+</button>
                                </div>
                            </div>

                        </div>
                    </template>

                    <!-- Upsell Carousel / Strip -->
                    <div class="pt-2 space-y-2.5" x-show="cartItems.length > 0">
                        <div class="text-xs font-black text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🥤</span>
                            <span>GỢI Ý THÊM MÓN NGON:</span>
                        </div>
                        <div class="flex gap-2.5 overflow-x-auto pb-2 scrollbar-none">
                            <template x-for="up in upsellItems" :key="up.id">
                                <div class="shrink-0 bg-white border border-gray-200 rounded-full px-3.5 py-1.5 flex items-center gap-2 shadow-xs">
                                    <span class="text-sm" x-text="up.icon">🥤</span>
                                    <span class="text-xs font-bold text-gray-800 whitespace-nowrap" x-text="up.name">Coca Cola</span>
                                    <span class="text-xs font-black text-red-600" x-text="'+' + formatCurrency(up.price)">+12.000đ</span>
                                    <button 
                                        @click="addToCartDirect(up)" 
                                        class="text-[11px] font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-0.5 rounded-full"
                                    >+ Thêm</button>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                <!-- Drawer Footer & Payment Summary -->
                <div x-show="cartItems.length > 0" class="p-6 bg-white border-t border-gray-200/80 space-y-4 shrink-0 shadow-lg">
                    <div class="space-y-2 text-xs font-bold text-gray-600">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tạm tính:</span>
                            <span class="text-gray-900 font-black" x-text="formatCurrency(totalPrice)">313.000đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Phí giao hàng:</span>
                            <span class="text-emerald-600 font-bold" x-text="totalPrice >= 100000 ? '0đ (Freeship)' : '15.000đ'">0đ (Freeship)</span>
                        </div>
                        <div class="flex justify-between items-center text-base pt-2 border-t border-dashed border-gray-200">
                            <span class="font-black text-gray-900">Tổng cộng:</span>
                            <span class="text-2xl font-black text-red-600" x-text="formatCurrency(totalPrice >= 100000 ? totalPrice : totalPrice + 15000)">313.000đ</span>
                        </div>
                    </div>

                    <!-- Big Checkout Button -> Opens Checkout Modal -->
                    <button 
                        @click="openCheckout()" 
                        type="button" 
                        class="w-full py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-base tracking-wider uppercase shadow-xl red-glow text-center transition-all active:scale-95"
                    >
                        THANH TOÁN
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL 2: THANH TOÁN NHANH (HÀ NỘI) - MATCHING SCREENSHOT 5 & 6 EXACTLY -->
    <div 
        x-show="openCheckoutModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-checkout-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div 
                x-show="openCheckoutModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
                @click="openCheckoutModal = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Content Box -->
            <div 
                x-show="openCheckoutModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full max-h-[92vh] flex flex-col"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <h3 class="text-xl font-black text-gray-900" id="modal-checkout-title">
                        Thanh toán nhanh (Hà Nội)
                    </h3>
                    <button 
                        @click="openCheckoutModal = false" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="px-6 py-5 overflow-y-auto space-y-5 flex-1">
                    
                    <!-- Delivery Time Banner -->
                    <div class="bg-[#FFF5F5] border border-red-100 rounded-2xl p-3.5 flex items-center gap-2.5 text-xs sm:text-sm font-bold text-red-600">
                        <span class="text-lg">🛵</span>
                        <span><strong>Giao tiêu chuẩn:</strong> Dự kiến đến sau 25 – 40 phút</span>
                    </div>

                    <!-- Input 1: Họ và tên -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Họ và tên *
                        </label>
                        <input 
                            type="text" 
                            x-model="checkoutForm.fullName"
                            placeholder="Ví dụ: Nguyễn Văn A"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                        >
                    </div>

                    <!-- Input 2: Số điện thoại -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Số điện thoại *
                        </label>
                        <input 
                            type="tel" 
                            x-model="checkoutForm.phone"
                            placeholder="Ví dụ: 0912 345 678"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                        >
                    </div>

                    <!-- Input 3: Khu vực quận (Hà Nội) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Khu vực quận (Hà Nội) *
                        </label>
                        <div class="relative">
                            <select 
                                x-model="checkoutForm.district"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-bold text-gray-800 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 appearance-none pr-10 cursor-pointer"
                            >
                                <template x-for="dist in districts" :key="dist">
                                    <option :value="dist" x-text="dist"></option>
                                </template>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Input 4: Địa chỉ chi tiết -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Địa chỉ chi tiết *
                        </label>
                        <input 
                            type="text" 
                            x-model="checkoutForm.address"
                            placeholder="Số nhà, ngõ, tên toà nhà..."
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                        >
                    </div>

                    <!-- Input 5: Ghi chú cho tài xế giao hàng -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Ghi chú cho tài xế giao hàng
                        </label>
                        <input 
                            type="text" 
                            x-model="checkoutForm.driverNote"
                            placeholder="Ví dụ: Gọi trước khi đến, gửi bảo vệ..."
                            class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white text-xs sm:text-sm font-medium focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 placeholder:text-gray-400"
                        >
                    </div>

                    <!-- Section: Phương thức thanh toán -->
                    <div class="space-y-2 pt-1">
                        <label class="block text-xs sm:text-sm font-black text-gray-900">
                            Phương thức thanh toán
                        </label>
                        <div class="grid grid-cols-2 gap-2.5">
                            
                            <!-- Option 1: COD -->
                            <button 
                                @click="checkoutForm.paymentMethod = 'cod'"
                                type="button"
                                class="p-3.5 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                :class="checkoutForm.paymentMethod === 'cod' ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                            >
                                <span class="text-base">💵</span>
                                <span class="text-xs font-black text-gray-900">Tiền mặt (COD)</span>
                            </button>

                            <!-- Option 2: MoMo -->
                            <button 
                                @click="checkoutForm.paymentMethod = 'momo'"
                                type="button"
                                class="p-3.5 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                :class="checkoutForm.paymentMethod === 'momo' ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                            >
                                <span class="w-3.5 h-3.5 rounded-full bg-[#A50064] inline-block"></span>
                                <span class="text-xs font-black text-gray-900">Ví MoMo</span>
                            </button>

                            <!-- Option 3: VNPay QR -->
                            <button 
                                @click="checkoutForm.paymentMethod = 'vnpay'"
                                type="button"
                                class="p-3.5 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                :class="checkoutForm.paymentMethod === 'vnpay' ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                            >
                                <span class="text-base">💳</span>
                                <span class="text-xs font-black text-gray-900">VNPay QR</span>
                            </button>

                            <!-- Option 4: ZaloPay -->
                            <button 
                                @click="checkoutForm.paymentMethod = 'zalopay'"
                                type="button"
                                class="p-3.5 rounded-2xl border-2 text-left flex items-center gap-2.5 transition-all"
                                :class="checkoutForm.paymentMethod === 'zalopay' ? 'border-red-500 bg-red-50/70 shadow-xs' : 'border-gray-200/80 bg-white hover:border-gray-300'"
                            >
                                <span class="w-3.5 h-3.5 rounded-full bg-[#0068FF] inline-block"></span>
                                <span class="text-xs font-black text-gray-900">ZaloPay</span>
                            </button>

                        </div>
                    </div>

                    <!-- Summary Box: MÓN ĐÃ CHỌN -->
                    <div class="bg-[#FAF6F0] rounded-2xl p-4 border border-orange-200/60 space-y-3">
                        <div class="text-xs font-black text-gray-700 uppercase tracking-wider">
                            MÓN ĐÃ CHỌN:
                        </div>

                        <div class="space-y-1.5 text-xs font-bold text-gray-800">
                            <template x-for="(item, idx) in cartItems" :key="idx">
                                <div class="flex items-center justify-between">
                                    <div class="truncate pr-4 text-gray-700">
                                        <span x-text="item.quantity + '× ' + item.name"></span>
                                    </div>
                                    <div class="font-black text-gray-900 shrink-0" x-text="formatCurrency(item.price * item.quantity)">
                                        147.000đ
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Total Summary -->
                        <div class="pt-3 border-t border-dashed border-gray-300 flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-900">Tổng thanh toán:</span>
                            <span class="text-xl font-black text-red-600" x-text="formatCurrency(totalPrice >= 100000 ? totalPrice : totalPrice + 15000)">
                                313.000đ
                            </span>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer: Submit Order Button -->
                <div class="p-4 sm:p-5 bg-white border-t border-gray-100 shrink-0">
                    <button 
                        @click="submitOrder()" 
                        type="button" 
                        class="w-full py-4 rounded-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-base tracking-wide uppercase shadow-xl red-glow text-center transition-all active:scale-95"
                    >
                        Xác nhận đặt đơn
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL 3: ĐẶT ĐƠN THÀNH CÔNG -->
    <div 
        x-show="openSuccessModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="openSuccessModal = false"></div>

            <div class="inline-block bg-white rounded-3xl text-center overflow-hidden shadow-2xl p-8 max-w-sm w-full relative z-10 space-y-5">
                <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-4xl shadow-inner animate-bounce">
                    🎉
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-2xl font-black text-gray-900">ĐẶT HÀNG THÀNH CÔNG!</h3>
                    <p class="text-xs text-gray-500">Mã đơn hàng của bạn là: <strong class="text-red-600 text-sm" x-text="orderSuccessData.orderCode">#GAO-83921</strong></p>
                </div>

                <div class="bg-[#FAF6F0] p-4 rounded-2xl text-xs space-y-1.5 text-left border border-orange-100">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Người nhận:</span>
                        <span class="font-bold text-gray-900" x-text="checkoutForm.fullName || 'Khách hàng'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Số điện thoại:</span>
                        <span class="font-bold text-gray-900" x-text="checkoutForm.phone"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Địa chỉ:</span>
                        <span class="font-bold text-gray-900 truncate max-w-[180px]" x-text="checkoutForm.address + ', ' + checkoutForm.district"></span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-gray-200">
                        <span class="text-gray-500">Tổng tiền:</span>
                        <span class="font-black text-red-600" x-text="formatCurrency(orderSuccessData.totalAmount)"></span>
                    </div>
                </div>

                <p class="text-[11px] text-gray-500 italic">Bếp đang chuẩn bị món nóng giòn. Shipper sẽ giao đến bạn sau 25-40 phút!</p>

                <button 
                    @click="openSuccessModal = false; switchView('home')" 
                    class="w-full py-3 rounded-full bg-red-600 text-white font-bold text-xs uppercase tracking-wider shadow-md hover:bg-red-700 transition-all"
                >
                    Tiếp tục đặt món
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div 
        x-show="toast.show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4 scale-90"
        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 transform translate-y-4 scale-90"
        class="fixed bottom-20 md:bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 border border-gray-700"
        x-cloak
    >
        <span class="text-xl">✅</span>
        <div>
            <div class="font-extrabold text-xs" x-text="toast.title">Đã thêm món!</div>
            <div class="text-[11px] text-gray-300" x-text="toast.message">Món đã được thêm vào giỏ hàng.</div>
        </div>
    </div>

</body>
</html>
