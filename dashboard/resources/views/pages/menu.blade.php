@extends('dashboard::layouts.default')

@section('content')

<layout-base v-cloak>
    <layout-menu></layout-menu>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/menu.js') }}"></script>
@endsection
