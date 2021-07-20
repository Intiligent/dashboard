@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/order.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin el-flex el-flex-middle">
    <div class="el-width-auto@md">
        <h2 class="el-text-bold">Заказы</h2>
    </div>
    <div class="el-width-expand@md">
        <span class="el-tag el-tag--success el-tag--mini">{{ $collection->count() }} из {{ $collection->total() }}</span>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.order', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Создать заказ</span>
            </a>
        </el-button-group>

        <el-tooltip class="item" effect="dark" content="Фильтр заказов" placement="bottom">
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
    <el-breadcrumb-item>Заказы</el-breadcrumb-item>
</el-breadcrumb>

<el-table class="el-width-1-1" :data="collection.data">
    <el-table-column prop="id" label="ID" width="40">
        <template slot-scope="{ row, column, $index }">
            <tt v-text="row.id"></tt>
        </template>
    </el-table-column>
    <el-table-column prop="created_at" label="Дата создания">
        <template slot-scope="{ row, column, $index }">
            <a class="el-link is-underline" :href="route('dashboard.order', row.id)" v-text="row.created_at"></a>
        </template>
    </el-table-column>
    <el-table-column prop="status.name" label="Статус">
        <template slot-scope="{ row, column, $index }">
            <a :href="route('dashboard.orderList', {status: row.status.code})" class="el-tag el-tag--mini" :class="'el-tag--' + row.status.class_name" v-text="row.status.name"></a>
        </template>
    </el-table-column>
    <el-table-column prop="created_at" label="Клиент">
        <template slot-scope="{ row, column, $index }">
            <div class="el-grid el-grid-mini">
                <div class="">
                    <el-avatar :size="30" :src="row.client.avatar"></el-avatar>
                </div>
                <div class="">
                    <div class="" style="margin-bottom: 3px;" v-text="row.client.name"></div>
                    <small class="el-text-muted" v-text="row.client.phone || 'номер не указан'"></small>
                </div>
            </div>
        </template>
    </el-table-column>
    <el-table-column prop="amount" label="Сумма">
        <template slot-scope="{ row, column, $index }">
            <span v-text="row.amount + ' грн.'"></span>
        </template>
    </el-table-column>
    <el-table-column prop="created_at" label="Комментарий">
        <template slot-scope="{ row, column, $index }">
            <span v-text="row.comment"></span>
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
<script type="text/javascript" src="{{ mix('dashboard-panel/script/order.js') }}"></script>
@endsection
