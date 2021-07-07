@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/order.css') }}">
@endsection

@section('content')
<div class="">
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Заказ #{{ $order->id ?: 'новый' }}</h2>
    </div>
    <div class="el-width-auto@md">
        <el-button-group>
            <a href="{{ route('dashboard.order', 'new') }}" class="el-button el-button--default el-button--medium">
                <i class="el-icon-plus"></i><span>Добавить заказ</span>
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
    <el-breadcrumb-item><a href="{{ route('dashboard.orderList') }}">Заказы</a></el-breadcrumb-item>
    <el-breadcrumb-item>{{ $order->id ?: 'новый' }}</el-breadcrumb-item>
</el-breadcrumb>

<form class="el-margin-md" @submit.prevent="submit" method="post">
    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-1-2@md">
            <div class="">
                <div class="">{{ $order->created_at }}</div>
                <div class="">{{ $order->status->name }}</div>
                <div class="">{{ $order->ip_address }}</div>
                <div class="">{{ $order->comment }}</div>
            </div>
        </div>
        <div class="el-width-1-2@md">
            <div class="el-grid">
                <div class="">
                    <el-avatar shape="square" :size="100" src="{{ $order->client->avatar }}"></el-avatar>
                </div>
                <div class="">
                    <div class="">{{ $order->client->name }}</div>
                    <div class="">{{ $order->client->phone }}</div>
                    <div class="">{{ $order->client->email }}</div>
                    <div class="">{{ $order->client->created_at }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="el-margin">
        <el-table :data="order.products">
            <el-table-column prop="product.product_id" label="ID" width="50">
                <template slot-scope="{ row, column, $index }">
                    <tt v-text="row.product_id"></tt>
                </template>
            </el-table-column>
            <el-table-column prop="product.image.path" width="52">
                <template slot-scope="{ row, column, $index }">
                    <span class="el-avatar el-avatar--square" style="height: 32px; width: 32px; line-height: 32px;">
                        <img loading="lazy" :src="row.product.image.path" style="object-fit: cover;">
                    </span>
                </template>
            </el-table-column>
            <el-table-column prop="product.name" label="Наименование">
                <template slot-scope="{ row, column, $index }">
                    <a class="el-link is-underline" :href="route('dashboard.product', row.product_id)" v-text="row.product.name"></a>
                </template>
            </el-table-column>
            <el-table-column prop="product.category.name" label="Категория">
                <template slot-scope="{ row, column, $index }">
                    <a class="el-tag el-tag--mini el-tag--info" :href="route('dashboard.productList', {category: row.category_id})" v-text="row.product.category.name"></a>
                </template>
            </el-table-column>
            <el-table-column prop="product.price" label="Количество" width="120">
                <template slot-scope="{ row, column, $index }">
                    <span v-text="row.count"></span>
                </template>
            </el-table-column>
            <el-table-column prop="product.price" label="Цена" width="100">
                <template slot-scope="{ row, column, $index }">
                    <span v-text="row.product.price + ' ₴'"></span>
                </template>
            </el-table-column>
            <table slot="append" class="el-table__body el-width-1-1">
                <tbody>
                    <tr class="el-table__row">
                        <td>summary</td>
                        <td colspan="3">summary</td>
                        <td>summary</td>
                    </tr>
                </tbody>
            </table>
        </el-table>
    </div>

    <div class="el-grid el-flex el-flex-middle el-margin">
        <div class="el-width-expand@md"></div>
        <div class="el-width-auto@md">
            <el-button native-type="submit" size="medium" type="primary" :loading="state.isLoading">Сохранить</el-button>
        </div>
    </div>
</form>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/script/order.js') }}"></script>
@endsection
