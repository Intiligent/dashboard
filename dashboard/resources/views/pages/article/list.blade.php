@extends('dashboard::layouts.default')

@section('content')
<layout-base v-cloak>
    <layout-article-list></layout-article-list>
</layout-base>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/article-list.js') }}"></script>
@endsection
