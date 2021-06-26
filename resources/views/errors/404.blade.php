@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/menu.css') }}">
@endsection

@section('content')
<div class="el-text-center el-margin-lg-top">
    <img src="/img/dashboard/404.jpg" alt="404">
    <h3 class="el-text-bold">404. Page not found.</h3>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/script/menu.js') }}"></script>
@endsection
