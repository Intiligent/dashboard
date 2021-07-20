@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/role.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin el-flex el-flex-middle">
    <div class="el-width-auto@md">
        <h2 class="el-text-bold">Роли</h2>
    </div>
    <div class="el-width-expand@md">
        <span class="el-tag el-tag--success el-tag--mini">{{ $collection->count() }} из {{ $collection->total() }}</span>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.role', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Create role</span>
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
    <el-breadcrumb-item>Roles</el-breadcrumb-item>
</el-breadcrumb>

<el-table class="el-width-1-1" :data="tableRows" @sort-change="onSortChange" :default-sort="defaultSort">
    <el-table-column prop="id" label="ID" width="56" sortable="custom">
        <template slot-scope="{ row, column, $index }">
            <pre v-text="row.id"></pre>
        </template>
    </el-table-column>
    <el-table-column prop="name" label="Name" sortable="custom">
        <template slot-scope="{ row, column }">
            <el-link type="primary" :href="route('dashboard.role', row.id)" v-text="row.name"></el-link>
        </template>
    </el-table-column>
    <el-table-column prop="users_count" label="Users" sortable="users_count" width="100">
        <template slot-scope="{ row, column }">
            <span v-text="row.users_count"></span>
        </template>
    </el-table-column>
    <el-table-column prop="permissions_count" label="Permissions" sortable="permissions_count" width="140">
        <template slot-scope="{ row, column }">
            <span v-text="row.permissions_count"></span>
        </template>
    </el-table-column>
    <el-table-column prop="created_at" label="Created at" sortable="custom" width="165">
        <template slot-scope="{ row, column }">
            <pre v-text="row.created_at"></pre>
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
<script type="text/javascript" src="{{ mix('dashboard-panel/script/article.js') }}"></script>
@endsection
