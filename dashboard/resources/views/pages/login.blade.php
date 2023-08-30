@extends('dashboard::layouts.simple')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/login.css') }}">
@endsection

@section('header')
@endsection

@section('content')
<el-container>
    <el-form method="POST" label-position="top">
        <h1 class="el-margin el-text--bold">Login</h1>
        {{ csrf_field() }}
        @if ($errors->any())
            @foreach($errors->all() as $message)
                <div role="alert" class="el-alert el-alert--warning is-light">
                    <i class="el-alert__icon el-icon-warning"></i>
                    <div class="el-alert__content">
                        <span class="el-alert__title">{{ $message }}</span>
                    </div>
                </div>
            @endforeach
        @endif
        <el-form-item label="Email">
            <el-input
                name="email"
                placeholder="example@gmail.com"
                v-model="form.login"
            />
        </el-form-item>
        <el-form-item label="Password">
            <el-input
                type="password"
                name="password"
                placeholder="your super password"
                v-model="form.password"
            />
        </el-form-item>
        <el-form-item>
            <el-button native-type="submit" type="primary">Login</el-button>
        </el-form-item>
    </el-form>
</el-container>
@endsection

@section('footer')
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('dashboard/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('dashboard/js/login.js') }}"></script>
@endsection
