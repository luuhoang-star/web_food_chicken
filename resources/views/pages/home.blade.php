@extends('layouts.app')

@section('title', 'GAO - Gà Sốt & Cơm Hà Nội | Khám Phá Gà Giòn Sốt Đậm Vị')

@section('content')
    <!-- HERO DISCOVERY -->
    @include('sections.hero')

    <!-- SPOTLIGHT 4 VỊ SỐT (TEASER BANNER DẪN ĐẾN TRANG SỐT) -->
    @include('sections.home-sauces-banner')

    <!-- TOP MÓN HOT BÁN CHẠY -->
    @include('sections.popular-dishes')

    <!-- COMBO TIẾT KIỆM -->
    @include('sections.combos')

    <!-- 3 CAM KẾT CHẤT LƯỢNG VÀNG -->
    @include('sections.benefits')

    <!-- ĐÁNH GIÁ TỪ KHÁCH HÀNG HÀ NỘI -->
    @include('sections.testimonials')
@endsection
