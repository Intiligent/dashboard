@extends('dashboard::layouts.default')

@section('content')
<layout-base v-cloak>
    <layout-view></layout-view>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/error.js') }}"></script>
@endsection
