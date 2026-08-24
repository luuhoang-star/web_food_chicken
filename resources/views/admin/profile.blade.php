@extends('layouts.admin')

@section('title', 'Tài Khoản & Đổi Mật Khẩu')
@section('page_title', '🔐 Tài Khoản & Đổi Mật Khẩu')

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- CARD 1: THÔNG TIN TÀI KHOẢN -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>👤</span>
                    <span>Thông Tin Tài Khoản</span>
                </h3>
                <p class="text-xs text-gray-500">Cập nhật họ tên và địa chỉ email đăng nhập của bạn.</p>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Họ và Tên <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $user->name) }}" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                    @error('name')
                        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Địa Chỉ Email Đăng Nhập <span class="text-red-500">*</span></label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $user->email) }}" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none font-mono"
                    >
                    @error('email')
                        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                >
                    Lưu Thay Đổi Thông Tin
                </button>
            </form>
        </div>

        <!-- CARD 2: ĐỔI MẬT KHẨU BẢO MẬT -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="font-black text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🔑</span>
                    <span>Đổi Mật Khẩu Mới</span>
                </h3>
                <p class="text-xs text-gray-500">Đổi mật khẩu định kỳ để bảo vệ tài khoản quản trị của bạn.</p>
            </div>

            <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-3.5">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Mật Khẩu Hiện Tại <span class="text-red-500">*</span></label>
                    <input 
                        type="password" 
                        name="current_password" 
                        placeholder="••••••••" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                    @error('current_password')
                        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Mật Khẩu Mới (Tối thiểu 6 ký tự) <span class="text-red-500">*</span></label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="••••••••" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                    @error('password')
                        <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold text-gray-700">Xác Nhận Mật Khẩu Mới <span class="text-red-500">*</span></label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        placeholder="••••••••" 
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs font-medium text-gray-900 focus:bg-white focus:border-red-500 outline-none"
                    >
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white font-black text-xs uppercase tracking-wider shadow-sm transition-all cursor-pointer"
                >
                    Cập Nhật Mật Khẩu
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
