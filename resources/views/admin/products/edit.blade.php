@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Món: ' . $product->name)
@section('page_title', '🍗 Chỉnh Sửa Chi Tiết Món Ăn')

@section('content')
    @include('admin.products._form')
@endsection
