<!DOCTYPE html>
<html lang="ru">
<head>
    @include('dashboard::components.head')
</head>
<body>
    <div id="root">
        @yield('content')
    </div>

    <!-- @ routes('dashboard') -->
    <!-- !! shared()->render() !!} -->

    @yield('js')
</body>
</html>
