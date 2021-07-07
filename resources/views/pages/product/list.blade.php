@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/product.css') }}">
@endsection

@section('header.center')
<div class="el-width-expand">
    <form class="" method="get">
        <el-autocomplete class="el-width-1-1" name="q" :trigger-on-focus="false" prefix-icon="el-icon-search4" size="medium" placeholder="Введите название товара" :debounce="500" v-model="q" value-key="name" :fetch-suggestions="onSuggest" clearable>
            <el-button native-type="submit" slot="append">Поиск</el-button>
            <template slot-scope="{ item: row }">
                <div class="el-grid el-grid-sm el-flex el-flex-middle suggest-item">
                    <div class="">
                        <span class="el-avatar el-avatar--square" style="height: 32px; width: 32px; line-height: 32px; top: 7px; position: relative;">
                            <img loading="lazy" :src="row.image.path" style="object-fit: cover;">
                        </span>
                    </div>
                    <div class="">
                        <div class="suggest-title"><span v-text="row.name"></span><small class="el-margin-left el-text-primary" v-text="row.price + ' грн.'"></small></div>
                        <div class="suggest-subtitle" v-text="row.category.name"></div>
                    </div>
                </div>
            </template>
        </el-autocomplete>
    </form>
</div>
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin el-flex el-flex-middle">
    <div class="el-width-auto@md">
        <h2 class="el-text-bold">Товары</h2>
    </div>
    <div class="el-width-expand@md">
        <span class="el-tag el-tag--success el-tag--mini">{{ $collection->count() }} из {{ $collection->total() }}</span>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.product', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Добавить товар</span>
            </a>
        </el-button-group>

        <el-tooltip class="item" effect="dark" content="Фильтр товаров" placement="bottom">
            <el-button size="medium" class="is-narrow" plain type="primary" icon="el-icon-filter4" @click="drawer.filter = true"></el-button>
        </el-tooltip>

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
    <el-breadcrumb-item>Список товаров</el-breadcrumb-item>
</el-breadcrumb>

<el-table :data="collection.data" @sort-change="onSortChange">
    <el-table-column prop="id" label="ID" width="50">
        <template slot-scope="{ row, column, $index }">
            <tt v-text="row.id"></tt>
        </template>
    </el-table-column>
    <el-table-column prop="image.path" width="48">
        <template slot-scope="{ row, column, $index }">
            <span class="el-avatar el-avatar--square" style="height: 32px; width: 32px; line-height: 32px;">
                <img loading="lazy" :src="row.image.path" style="object-fit: cover;">
            </span>
        </template>
    </el-table-column>
    <el-table-column prop="name" label="Имя">
        <template slot-scope="{ row, column, $index }">
            <a class="el-link is-underline" :href="route('dashboard.product', row.id)" v-text="row.name"></a>
        </template>
    </el-table-column>
    <el-table-column prop="category.name" label="Категория" width="140">
        <template slot-scope="{ row, column, $index }">
            <a class="el-tag el-tag--mini el-tag--info" :href="route('dashboard.productList', {category: row.category_id})" v-text="row.category.name" v-if="row.category"></a><span v-else>-</span>
        </template>
    </el-table-column>
    <el-table-column prop="slug" label="Ссылка">
        <template slot-scope="{ row, column, $index }">
            <a class="el-link is-underline" target="_blank" :href="route('face.product', {category: row.category.slug, product: row.slug})" v-text="row.slug" v-if="row.category"></a><span v-else v-text="row.slug"></span> <small class="el-text-muted el-icon-new-tab2"></small>
        </template>
    </el-table-column>
    <el-table-column prop="price" label="Цена" width="80" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <span v-text="row.price + ' ₴'"></span>
        </template>
    </el-table-column>
    <el-table-column prop="rate" label="R" width="64" sortable="custom"></el-table-column>
    <el-table-column width="56">
        <template slot-scope="{ row, column, $index }">
            <el-switch v-model="row.active" active-color="#67C23A" @change="onActiveChange(row, $event)"></el-switch>
        </template>
    </el-table-column>
    <el-table-column align="right" width="34">
        <template slot-scope="{ row, column, $index }">
            <el-button size="mini" type="danger" icon="el-icon-cross2" circle @click="destroy(row, $index)"></el-button>
        </template>
    </el-table-column>
</el-table>

<div class="el-margin-md">
    {{ $collection->withQueryString()->links('dashboard::vendor.pagination.default') }}
</div>

<el-drawer title="Фильтр товаров" :visible.sync="drawer.filter" size="22%">
    <form class="" method="post">
        <div class="el-margin">
            <div class="el-margin-sm"><label for="filters.price">Цена</label></div>
            <el-slider id="filters.price" v-model="filters.price" range :max="100"></el-slider>
        </div>
        <div class="el-margin">
            <div class="el-margin-sm"><label for="filters.category">Категория</label></div>
            <el-select id="filters.category" class="el-width-1-1" v-model="filters.category_id" multiple filterable placeholder="Выберите категорию товара" @blur.stop>
                <el-option v-for="category in categories" :key="category.id" :label="'-'.repeat(category.depth) + ' ' + category.name" :value="category.id"></el-option>
            </el-select>
        </div>
        <div class="el-margin">
            <div class="el-margin-sm"><label for="filters.active">Активность</label></div>
            <el-checkbox-group v-model="filters.active">
                <el-checkbox label="1"><span>Активный</span></el-checkbox>
                <el-checkbox label="0"><span>Неактивный</span></el-checkbox>
            </el-checkbox-group>
        </div>
        <div class="el-margin el-text-right">
            <el-button native-type="submit" size="medium" type="primary">Найти</el-button>
        </div>
    </form>
</el-drawer>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/script/product.js') }}"></script>
@endsection
