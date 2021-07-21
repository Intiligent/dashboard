@extends('dashboard::layouts.simple')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/login.css') }}">
@endsection

@section('header')
@endsection

@section('content')
<div class="el-grid el-grid-collapse el-child-width-1-2@md el-height-viewport">
    <div class="el-position-relative el-background-muted">
        <div class="el-position-center el-text-right">
            <img src="/dashboard-panel/img/logo.png" alt="{{ config('APP_NAME') }}" style="height: 60px;">
            <h3 class="el-text-bold el-margin-sm-bottom">{{ config('APP_NAME') }}</h3>
            <div class="el-text-muted">{{ config('META_TITLE') }}</div>
        </div>
    </div>

    <div class="el-position-relative">
        <form class="el-position-center el-width-medium@md" action="{{ route('dashboard.postLogin') }}" method="post">
            <h1 class="el-margin el-text-bold">Login</h1>
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
            {{ csrf_field() }}
            <div class="el-margin">
                <div class="el-input el-input--prefix">
                    <input type="text" name="email" placeholder="Type your login" class="el-input__inner" required>
                    <span class="el-input__prefix"><i class="el-input__icon el-icon-user"></i></span>
                </div>
            </div>
            <div class="el-margin">
                <div class="el-input el-input--prefix">
                    <input type="password" name="password" placeholder="Type your password" class="el-input__inner" required>
                    <span class="el-input__prefix"><i class="el-input__icon el-icon-lock"></i></span>
                </div>
            </div>
            <div class="el-margin el-grid el-flex-middle">
                <div class="el-width-expand@md">
                    <label class=""><input type="checkbox" name="remember" style="vertical-align: middle;"> remember me</label>
                </div>
                <div class="el-width-auto@md">
                    <button type="submit" class="el-button el-button--primary">
                        <span>Login</span>
                        <i class="el-icon-arrow-right8"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer')
@endsection
