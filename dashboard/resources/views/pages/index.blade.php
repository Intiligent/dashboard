@extends('dashboard::layouts.default')

@section('content')

<layout-base v-cloak>
    <layout-home></layout-home>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/home.js') }}"></script>
@endsection
