@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/user.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin el-flex el-flex-middle">
    <div class="el-width-auto@md">
        <h2 class="el-text-bold">Пользователи</h2>
    </div>
    <div class="el-width-expand@md">
        <span class="el-tag el-tag--success el-tag--mini">{{ $collection->count() }} из {{ $collection->total() }}</span>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.user', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Новый пользователь</span>
            </a>
        </el-button-group>

        <el-tooltip class="item" effect="dark" content="Фильтр пользователей" placement="bottom">
            <el-button size="medium" class="is-narrow" plain type="primary" icon="el-icon-filter4"></el-button>
        </el-tooltip>

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
    <el-breadcrumb-item>Пользователи</el-breadcrumb-item>
</el-breadcrumb>

<el-table class="el-width-1-1" :data="tableRows" @sort-change="onSortChange" :default-sort="defaultSort">
    <el-table-column prop="id" label="ID" width="60" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <tt v-text="row.id"></tt>
        </template>
    </el-table-column>
    <el-table-column prop="avatar" width="46">
        <template slot-scope="{ row, column, $index }">
            <el-avatar :size="30" :src="row.avatar"></el-avatar>
        </template>
    </el-table-column>
    <el-table-column prop="name" label="Имя" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <a class="el-link is-underline" :href="route('dashboard.user', row.id)" v-text="row.name"></a>
        </template>
    </el-table-column>
    <el-table-column prop="email" label="Email" sortable="custom"></el-table-column>
    <el-table-column prop="phone" label="Телефон" sortable="custom"></el-table-column>
    <el-table-column prop="created_at" label="Дата создания" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <span v-text="row.created_at"></span>
        </template>
    </el-table-column>
    <el-table-column align="right" width="42">
        <template slot-scope="{ row, column, $index }">
            <!-- <el-button size="mini" type="danger" icon="el-icon-cross2" circle></el-button> -->
        </template>
    </el-table-column>
</el-table>

</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/user.js') }}"></script>
@endsection
