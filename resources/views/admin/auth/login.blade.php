<!DOCTYPE html>
<html lang="vi" class="h-full bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Nhập Quản Trị | GAO Gà Sốt & Cơm Hà Nội</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-gray-950 via-gray-900 to-black text-gray-100">

    <div class="max-w-md w-full space-y-6">
        
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-red-600 to-orange-500 flex items-center justify-center text-2xl text-white shadow-xl shadow-red-600/30">
                🍗
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">GAO ADMIN</h1>
            <p class="text-xs text-gray-400">Đăng nhập hệ thống quản trị bếp & đơn hàng online</p>
        </div>

        <!-- Login Card -->
        <div class="bg-gray-900/90 border border-gray-800 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-md space-y-5">
            
            @if(session('info'))
                <div class="p-3.5 rounded-xl bg-blue-950/60 border border-blue-800 text-blue-300 text-xs font-medium">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-gray-300 uppercase tracking-wider">Email Quản Trị</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            name="email" 
                            id="email"
                            value="{{ old('email', 'admin@gao.vn') }}" 
                            placeholder="admin@gao.vn" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-950 border border-gray-800 text-white placeholder-gray-600 text-sm font-medium focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all"
                            required 
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-gray-300 uppercase tracking-wider">Mật Khẩu</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            value="admin123"
                            placeholder="••••••••" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-950 border border-gray-800 text-white placeholder-gray-600 text-sm font-medium focus:border-red-500 focus:ring-2 focus:ring-red-500/20 outline-none transition-all"
                            required
                        >
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-400 hover:text-gray-200">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-gray-950 border-gray-700 text-red-600 focus:ring-red-500" checked>
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-lg shadow-red-600/30 hover:scale-[1.01] active:scale-95 transition-all duration-150 cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>Đăng Nhập Quản Trị</span>
                    <span>→</span>
                </button>
            </form>

            <div class="pt-3 border-t border-gray-800 text-center">
                <p class="text-[11px] text-gray-500">
                    Tài khoản mặc định: <strong class="text-gray-300 font-mono">admin@gao.vn</strong> • MK: <strong class="text-gray-300 font-mono">admin123</strong>
                </p>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-gray-300 transition-colors">
                ← Quay lại Trang Chủ Bán Hàng
            </a>
        </div>

    </div>

</body>
</html>
