@extends('dashboard::layouts.default')

@section('content')

<layout-base v-cloak>
    <layout-article-item></layout-article-item>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/article-item.js') }}"></script>
@endsection
