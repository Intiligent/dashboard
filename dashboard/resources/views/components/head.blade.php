<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta http-equiv="content-language" content="{{ config('app.locale') }}">
<meta name="viewport" content="initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width">
<meta name="csrf" content="{{ csrf_token() }}">

<title>{ ! ! Meta::get('title') !!}</title>
<meta name="description" content="{ ! ! Meta::get('description') !!}">
<meta name="keywords" content="{ ! ! Meta::get('keywords') !!}">

<!--OpenGraph | http://ogp.me-->
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('APP_NAME') }}">
<meta property="og:url" content="{{ Request::url() }}">
<meta property="og:title" content="{ ! ! Meta::get('title') !!}">
<meta property="og:description" content="{ ! ! Meta::get('description') !!}">
<meta property="og:image" content="">

<link rel="icon" type="image/x-icon" href="{{ asset('/dashboard/favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('/dashboard/favicon.png') }}" type="image/png">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('/dashboard/favicon.png') }}">

<!-- <link rel="stylesheet" href=" mix('style/face/index.css') }}"> -->

@yield('style')
