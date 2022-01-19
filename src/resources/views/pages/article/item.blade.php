@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/article.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Текстовая страница</h2>
    </div>
    <div class="el-width-auto@md">
        <el-dropdown trigger="click" @command="handleMenuCommand">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item icon="el-icon-copy4" command="copy">Дублировать страницу</el-dropdown-item>
                <el-dropdown-item class="el-text-danger" icon="el-icon-bin" command="destroy">Удалить</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<el-breadcrumb class="el-margin" separator="→">
    <el-breadcrumb-item><a href="{{ route('dashboard.home') }}">Главная</a></el-breadcrumb-item>
    <el-breadcrumb-item><a href="{{ route('dashboard.articleList') }}">Страницы</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $model->title ?: 'новая' }}</el-breadcrumb-item>
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
            <label class="el-form-label" for="title">Заголовок:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="title" placeholder="Введите заголовок страницы" prefix-icon="el-icon-paragraph-left3" v-model="model.title"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="file">Изображение:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-media-library :source="model.image" :model-id="model.id" model-type="article"></el-media-library>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="file">Галерея:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-media-library :source="model.gallery" :model-id="model.id" model-type="article" entity="gallery" multiple></el-media-library>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="slug">Ссылка:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" prefix-icon="el-icon-link" placeholder="Заполните ссылку текстовой страницы" v-model="model.slug">
                <el-button slot="append" @click="generateSlug">Создать ссылку</el-button>
            </el-input>
        </div>
    </div>

    <div class="el-margin">
        <ckeditor v-model="model.text" :config="editorConfig"></ckeditor>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="active">Активность:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-switch name="active" id="active" v-model="model.active" active-color="#13ce66"></el-switch>
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
        <div class="el-width-1-4@md">
            <label class="el-form-label">Просмотры:</label>
        </div>
        <div class="el-width-3-4@md">
            <span>{{ $model->views ?: 0 }}</span>
            @if ($model->slug)
                <a target="_blank" href="{{ route('face.page', $model->slug) }}" class="el-margin-left">перейти <i class="el-icon-arrow-right8"></i></a>
            @endif
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
<script type="text/javascript" src="{{ mix('dashboard-panel/script/article.js') }}"></script>
@endsection
