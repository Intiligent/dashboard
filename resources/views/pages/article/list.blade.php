@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/article.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin el-flex el-flex-middle">
    <div class="el-width-auto@md">
        <h2 class="el-text-bold">Страницы</h2>
    </div>
    <div class="el-width-expand@md">
        <span class="el-tag el-tag--success el-tag--mini">{{ $collection->count() }} из {{ $collection->total() }}</span>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.article', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Создать страницу</span>
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
    <el-breadcrumb-item>Страницы</el-breadcrumb-item>
</el-breadcrumb>

<el-table class="el-width-1-1" :row-class-name="tableRowClassName" :data="tableRows" @sort-change="onSortChange" :default-sort="defaultSort">
    <el-table-column prop="id" label="ID" width="56" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <span v-text="row.id"></span>
        </template>
    </el-table-column>
    <el-table-column prop="title" label="Заголовок" sortable="custom">
        <template slot-scope="{ row, column }">
            <el-link type="primary" :href="route('dashboard.article', row.id)" v-text="row.title"></el-link>
        </template>
    </el-table-column>
    <el-table-column prop="slug" label="Ссылка" sortable="custom">
        <template slot-scope="{ row, column }">
            <el-link target="_blank" :href="route('face.page', row.slug)" v-text="row.slug"></el-link> <small class="el-text-muted el-icon-new-tab2"></small>
        </template>
    </el-table-column>
    <el-table-column prop="created_at" label="Дата создания" sortable="custom">
        <template slot-scope="{ row, column }">
            <span v-text="row.created_at"></a>
        </template>
    </el-table-column>
    <el-table-column prop="active" width="56" sortable="custom">
        <template slot-scope="{ row, column }">
            <el-switch v-model="row.active" active-color="#67C23A" @change="onActiveChange(row, $event)"></el-switch>
        </template>
    </el-table-column>
    <el-table-column align="right" width="34">
        <template slot-scope="{ row, column, $index }">
            <el-button size="mini" type="danger" icon="el-icon-cross2" circle @click="onDestroy(row)"></el-button>
        </template>
    </el-table-column>
</el-table>

</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/script/article.js') }}"></script>
@endsection
