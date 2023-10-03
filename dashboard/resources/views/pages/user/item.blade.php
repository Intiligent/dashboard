@extends('dashboard::layouts.default')

@section('content')

<layout-base v-cloak>
    <layout-user-item></layout-user-item>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/user-item.js') }}"></script>
@endsection