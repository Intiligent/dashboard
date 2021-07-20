@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/product.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold" v-text="product.id ? product.name : 'новый'">{{ $product->name ?: 'новый' }}</h2>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.product', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Добавить товар</span>
            </a>
        </el-button-group>
        <el-dropdown trigger="click">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>Дублировать страницу</el-dropdown-item>
                <el-dropdown-item>Action 2</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<el-breadcrumb class="el-margin" separator="→">
    <el-breadcrumb-item><a href="{{ route('dashboard.home') }}">Главная</a></el-breadcrumb-item>
    <el-breadcrumb-item><a href="{{ route('dashboard.productList') }}">Список товаров</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $product->name ?: 'новый' }}</el-breadcrumb-item>
</el-breadcrumb>

<form class="el-margin-md" @submit.prevent="submit" method="post">
    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label">ID:</label>
        </div>
        <div class="el-width-3-4@md">
            <tt>{{ $product->id ?: 'new' }}</tt>
            <input type="hidden" name="id" value="{{ $product->id }}">
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="description">Изображение:</label>
        </div>
        <div class="el-width-3-4@md">
            <span class="el-avatar el-avatar--square" style="height: 250px; width: 250px; line-height: 250px;">
                <img loading="lazy" src="{{ $product->image->path }}" style="object-fit: cover;">
            </span>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="name">Заголовок:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input size="medium" class="el-width-1-1" id="name" placeholder="Введите заголовок страницы" prefix-icon="el-icon-paragraph-left3" v-model="product.name"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="category">Категория/Цена:</label>
        </div>
        <div class="el-width-2-4@md">
            <el-select id="category" class="el-width-1-1" size="medium" v-model="product.category_id" filterable placeholder="Выберите категорию товара">
                <el-option v-for="category in categories" :key="category.id" :label="'—'.repeat(category.depth) + ' ' + category.name" :value="category.id"></el-option>
                <template slot="prefix">
                    <i class="el-icon-folder4"></i>
                </template>
            </el-select>
        </div>
        <div class="el-width-1-4@md">
            <el-input-number class="el-width-1-1" id="price" placeholder="Введите цену" v-model="product.price" size="medium" :precision="2" :step="0.1" :min="0"></el-input-number>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="slug">Ссылка:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input id="slug" size="medium" prefix-icon="el-icon-link" placeholder="Заполните ссылку текстовой страницы" v-model="product.slug">
                <el-button type="warning" slot="append">Создать ссылку</el-button>
            </el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="description">Описание:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-input type="textarea" :rows="3" placeholder="Введите описание товара" v-model="product.description"></el-input>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label" for="active">Активность:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-switch name="active" id="active" v-model="product.active" active-color="#13ce66"></el-switch>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Дата создания/обновления:</label>
        </div>
        <div class="el-width-3-4@md">
            <el-tag plain type="info" size="mini">{{ $product->created_at ?: date('Y-m-d H:i:s') }}</el-tag> / <el-tag plain size="mini">{{ $product->updated_at ?: '-' }}</el-tag>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Просмотры:</label>
        </div>
        <div class="el-width-3-4@md">
            <span>{{ $product->views ?: 0 }}</span>
        </div>
    </div>

    <div class="el-grid el-flex el-flex-middle">
        <div class="el-width-1-4@md">
            <label class="el-form-label">Рейтинг:</label>
        </div>
        <div class="el-width-3-4@md">
            <span>{{ $product->rate }}</span>
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
<script type="text/javascript" src="{{ mix('dashboard-panel/script/product.js') }}"></script>
@endsection
