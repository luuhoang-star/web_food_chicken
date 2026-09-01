@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Combo: ' . $combo->name)
@section('page_title', '✏️ Chỉnh Sửa Combo Món Ăn')

@section('content')
    @include('admin.combos._form')
@endsection
