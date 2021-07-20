<!DOCTYPE html>
<html lang={{ str_replace('_', '-', app()->getLocale()) }}>
<head>
    @include('dashboard::components.head')
</head>
<body>
    <div id="root" class="container-expand-{{ Cookie::get('LAYOUT_BOX_SIZE') }}">
        @section('header')
            @include('dashboard::components.header')
        @show

        <div class="el-container content-container">
            <div class="el-grid el-grid-collapse">
                <div class="el-width-auto sidebar-container">
                    @section('navbar')
                        @include('dashboard::components.navbar')
                    @show
                </div>
                <div class="el-width-expand">
                    <div class="el-container el-margin-top el-margin-md-bottom container-body">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @routes('dashboard')
    {!! shared()->render() !!}

    <script type="text/javascript" src="{{ mix('dashboard-panel/script/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('dashboard-panel/script/vendor.js') }}"></script>
    @yield('js')
</body>
</html>
