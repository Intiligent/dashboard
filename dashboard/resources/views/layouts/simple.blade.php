<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('dashboard::components.head')
</head>
<body>
    <div id="root" class="el-height-1-1">
        @yield('content')
    </div>

    <!-- @ routes('dashboard') -->
    <!-- !! shared()->render() !!} -->

    @yield('js')
</body>
</html>
