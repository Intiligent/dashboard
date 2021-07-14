@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/role.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold"><span v-text="role.id ? role.name : 'new'">{{ $role->name ?: 'New' }}</span></h2>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.role', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Add role</span>
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
    <el-breadcrumb-item><a href="{{ route('dashboard.roles') }}">Roles</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $role->name ?: 'new' }}</el-breadcrumb-item>
</el-breadcrumb>

<form class="el-margin-md" @submit.prevent="handleSubmit" method="post">
    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">ID:</label>
        </div>
        <div class="el-width-3-4@md">
            <tt>{{ $role->id ?: 'new' }}</tt>
            <input type="hidden" name="id" value="{{ $role->id }}">
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="role.name">Имя:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="role.name" placeholder="Введите имя пользователя (системное имя. латинскими)" prefix-icon="el-icon-paragraph-left3" v-model="role.name"></el-input>
        </div>
    </div>

    <div class="el-grid el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="role.description">Assign permissions:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-checkbox-group class="checkbox-group-list" v-model="role.assign_permissions">
                @foreach ($permissions as $permission)
                    <el-checkbox :label="{{ $permission->id }}" key="{{ $permission->id }}">{{ $permission->name }}@if ($permission->description)<span class="description">{{ $permission->description }}</span>@endif<el-tag size="mini" type="{{ $permission->guard_name === 'web' ? 'warning' : 'primary' }}">{{ $permission->guard_name }}</el-tag></el-checkbox>
                @endforeach
            </el-checkbox-group>
            <el-link target="_blank" :href="route('dashboard.permission', 'new')" class="el-margin-sm-top">+ Добавить новую</el-link>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Дата создания/обновления:</label>
        </div>
        <div class="el-width-3-4@md">
            <pre>{{ $role->created_at ?: date('Y-m-d H:i:s') }} / {{ $role->updated_at ?: '-' }}</pre>
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
<script type="text/javascript" src="{{ mix('dashboard/script/role.js') }}"></script>
@endsection
