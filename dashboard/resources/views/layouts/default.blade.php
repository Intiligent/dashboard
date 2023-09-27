<!DOCTYPE html>
<html lang={{ str_replace('_', '-', app()->getLocale()) }}>
<head>
    @include('dashboard::components.head')
</head>
<body>
    <div id="root" class="el-height-1-1">
        @yield('content')

        <!-- <section class="el-container el-height-1-1">
            <aside class="el-aside">
                @ section('navbar')
                    @ include('dashboard::components.navbar')
                @show
            </aside>
            <section class="el-container is-vertical">
                <header class="el-header" style="padding-left: 30px; padding-right: 30px;">
                    @ section('header')
                        @ include('dashboard::components.header')
                    @show
                </header>
                <main class="el-main" style="padding: 20px 30px;">
                    @ yield('content')
                </main>
            </section> -->
        </section>
    </div>

    @routes('dashboard')
    {!! shared()->render() !!}

    <script type="text/javascript" src="{{ mix('dashboard/js/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('dashboard/js/vendor.js') }}"></script>
    @yield('js')
</body>
</html>
