@extends('dashboard::layouts.default')

@section('content')
<layout-base v-cloak>
    <layout-item></layout-item>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/role-item.js') }}"></script>
@endsection
