@extends('dashboard::layouts.default')

@section('content')

<layout-base v-cloak>
    <layout-settings></layout-settings>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/settings.js') }}"></script>
@endsection
