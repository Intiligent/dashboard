@extends('dashboard::layouts.simple')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/login.css') }}">
@endsection

@section('header')
@endsection

@section('content')
<el-container class="el-height-1-1" style="max-width: 100%;">
    <div class="el-grid el-child-width-1-2 el-width-1-1">
        <div class="el-flex el-flex-middle el-flex-center" style="background-color: var(--el-color-primary-light-9);">
            <div class="el-grid">
                <div class="">
                    <img src="/dashboard/img/logo-square.png" alt="Logo" style="max-height: 60px;" />
                </div>
                <div class="">
                    <div class="" style="line-height: 36px;">
                        <h2>Dashboard</h2>
                    </div>
                    <div class="el-text--placeholder">
                        Manage your data easily
                    </div>
                </div>
            </div>
        </div>
        <div class="el-flex el-flex-middle">
            <el-form method="POST" label-position="top" style="margin: 0 auto;">
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
                        size="large"
                        placeholder="example@gmail.com"
                        v-model="form.email"
                    >
                        <template #prefix>
                            <i class="el-icon-envelop2"></i>
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item label="Password">
                    <el-input
                        type="password"
                        name="password"
                        size="large"
                        placeholder="your super password"
                        v-model="form.password"
                    >
                        <template #prefix>
                            <i class="el-icon-lock"></i>
                        </template>
                    </el-input>
                </el-form-item>
                <div class="el-grid el-grid-collapse el-width-1-1 el-flex-middle">
                    <div class="el-width-expand">
                        <el-checkbox
                            name="remember"
                            v-model="form.remember"
                        >Remember me</el-checkbox>
                    </div>
                    <div class="el-width-auto">
                        <el-button
                            size="large"
                            native-type="submit"
                            type="primary"
                        >
                            Login<i class="el-icon-enter3 el-icon--right"></i>
                        </el-button>
                    </div>
                </div>
            </el-form>
        </div>
    </div>
</el-container>
@endsection

@section('footer')
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('dashboard/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('dashboard/js/login.js') }}"></script>
@endsection
