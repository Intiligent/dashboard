@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/dashboard.css') }}">
@endsection

@section('content')
<div class="">
    dashboard
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/dashboard.js') }}"></script>
@endsection
