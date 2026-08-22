@extends('layouts.app')

@section('title', 'GAO - Gà Sốt & Cơm Hà Nội | Gà Giòn Sốt Đậm Vị')

@section('content')
    <!-- VIEW 1: TRANG CHỦ LANDING PAGE -->
    <main x-show="currentView === 'home'">
        @include('sections.hero')
        @include('sections.sauces')
        @include('sections.popular-dishes')
        @include('sections.combos')
        @include('sections.benefits')
        @include('sections.testimonials')
    </main>

    <!-- VIEW 2: THỰC ĐƠN ĐẶT MÓN (MENU ĐẦY ĐỦ) -->
    <main x-show="currentView === 'menu'" class="py-8 sm:py-12" id="menu-grid" x-cloak>
        @include('sections.menu-grid')
    </main>
@endsection
