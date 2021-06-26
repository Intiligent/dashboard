@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/user.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold"><span v-text="model.id ? model.name : 'новый'">{{ $model->name ?: 'Новый' }}</span></h2>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.user', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Добавить пользователя</span>
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

<el-breadcrumb class="el-margin" separator="/">
    <el-breadcrumb-item><a href="{{ route('dashboard.home') }}">Главная</a></el-breadcrumb-item>
    <el-breadcrumb-item><a href="{{ route('dashboard.userList') }}">Список товаров</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $model->name ?: 'новый' }}</el-breadcrumb-item>
</el-breadcrumb>

<form class="el-margin-md" @submit.prevent="handleSubmit" method="post">
    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">ID:</label>
        </div>
        <div class="el-width-3-4@md">
            <tt>{{ $model->id ?: 'new' }}</tt>
            <input type="hidden" name="id" value="{{ $model->id }}">
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="description">Аватар:</label>
        </div>
        <div class="el-width-3-4@md">
            <span class="el-avatar el-avatar--square" style="height: 200px; width: 200px; line-height: 200px;">
                <img loading="lazy" src="{{ $model->avatar }}" style="object-fit: cover;">
            </span>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="model.name">Имя:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="model.name" placeholder="Введите имя пользователя" prefix-icon="el-icon-user" v-model="model.name"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="model.email">Email:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="model.email" placeholder="Введите email пользователя" prefix-icon="el-icon-envelop2" v-model="model.email"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="model.phone">Телефон:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="model.phone" placeholder="Введите телефон пользователя" prefix-icon="el-icon-iphone" v-model="model.phone"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="model.password">Пароль:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input id="model.password" size="medium" prefix-icon="el-icon-lock" placeholder="Пароль" v-model="model.password" show-password>
                <el-button type="warning" slot="append">Создать новый</el-button>
            </el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Дата создания/обновления:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-tag plain type="info" size="mini">{{ $model->created_at ?: date('Y-m-d H:i:s') }}</el-tag> / <el-tag plain size="mini">{{ $model->updated_at ?: '-' }}</el-tag>
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
<script type="text/javascript" src="{{ mix('dashboard/script/user.js') }}"></script>
@endsection
