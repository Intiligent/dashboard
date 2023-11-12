@extends('dashboard::layouts.default')

@section('content')
<layout-base v-cloak>
    <layout-list></layout-list>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/role-list.js') }}"></script>
@endsection
