@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/permission.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Permission: <span v-text="permission.id ? permission.name : 'new'">{{ $permission->name ?: 'New' }}</span></h2>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.permission', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Add permission</span>
            </a>
        </el-button-group>
        <el-dropdown trigger="click">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>Action 2</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<el-breadcrumb class="el-margin" separator="→">
    <el-breadcrumb-item><a href="{{ route('dashboard.home') }}">Главная</a></el-breadcrumb-item>
    <el-breadcrumb-item><a href="{{ route('dashboard.permissions') }}">Permissions</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $permission->name ?: 'new' }}</el-breadcrumb-item>
</el-breadcrumb>

<form class="el-margin-md" @submit.prevent="handleSubmit" method="post">
    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">ID:</label>
        </div>
        <div class="el-width-3-4@md">
            <tt>{{ $permission->id ?: 'new' }}</tt>
            <input type="hidden" name="id" value="{{ $permission->id }}">
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="permission.name">Name:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="permission.name" placeholder="Input permission name (system name)" prefix-icon="el-icon-paragraph-left3" v-model="permission.name"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="permission.description">Description:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input type="textarea" size="medium" class="el-width-1-1" id="permission.description" placeholder="Input description (not required)" v-model="permission.description"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="permission.name">Guard name:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-radio-group size="medium" v-model="permission.guard_name">
                @foreach ($guards as $guard)
                    <el-radio-button label="{{ $guard }}"></el-radio-button>
                @endforeach
            </el-radio-group>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Roles has permission:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-checkbox-group v-model="permission.assign_roles">
                @foreach ($roles as $role)
                    <el-checkbox :label="{{ $role->id }}" key="{{ $role->id }}" class="el-margin-xs-bottom el-display-block">{{ $role->name }}</el-checkbox>
                @endforeach
            </el-checkbox-group>
            <el-link target="_blank" :href="route('dashboard.role', 'new')" class="el-margin-sm-top">+ Добавить новую</el-link>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Created at / Updated at:</label>
        </div>
        <div class="el-width-3-4@md">
            <pre>{{ $permission->created_at ?: date('Y-m-d H:i:s') }} / {{ $permission->updated_at ?: '-' }}</pre>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-expand@md"></div>
        <div class="el-width-auto@md">
            <el-button native-type="submit" size="medium" type="primary" :loading="state.isLoading">Сохранить</el-button>
        </div>
    </div>
</form>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/permission.js') }}"></script>
@endsection
