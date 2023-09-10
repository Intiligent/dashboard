@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/menu.css') }}">
@endsection

@section('content')

<layout-base v-cloak>
    <layout-menu></layout-menu>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/menu.js') }}"></script>
@endsection
