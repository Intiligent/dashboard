@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/user-list.css') }}">
@endsection

@section('content')

<layout-base v-cloak>
    <layout-user-list></layout-user-list>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/user-list.js') }}"></script>
@endsection
