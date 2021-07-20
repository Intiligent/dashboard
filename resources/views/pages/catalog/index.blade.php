@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/catalog.css') }}">
@endsection

@section('content')
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Каталог</h2>
    </div>
    <div class="el-width-auto@md">
        <el-dropdown trigger="click" @command="handleCommand">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item icon="el-icon-plus3" command="add">Добавить категорию</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<div class="el-margin">
    <el-tree ref="tree" :data="tree" :props="{label: 'name'}" node-key="id" draggable default-expand-all :filter-node-method="onFilterNode" @node-drop="onNodeDrop">
        <template v-slot="{node, data: model}">
            <div class="el-grid el-grid-sm el-flex el-flex-middle el-tree-node-container">
                <div class="el-width-2-5">
                    <a href="#" v-text="model.name" @click.prevent.stop="onCategoryEdit(model, node)"></a> <small class="el-text-muted" v-text="'(' + (model.products_count || 0) + ')'"></small>
                </div>
                <div class="el-width-expand">
                    <small><tt v-text="model.id"></tt>:</small> <span v-text="model.slug"></span>
                </div>
                <div class="el-width-auto">
                    <el-tooltip content="Добавить категорию" placement="top">
                        <el-button class="is-narrow" type="primary" size="mini" icon="el-icon-plus2" plain @click.prevent.stop="onAppendNode(model, node)"></el-button>
                    </el-tooltip>
                </div>
                <div class="el-width-auto">
                    <el-switch v-model="model.active" active-color="#13ce66" @change="onActiveChange(model, $event)"></el-switch>
                </div>
                <div class="el-width-auto">
                    <el-link type="info" icon="el-icon-cancel-circle2" @click.prevent.stop="onDestroy(model, node)"></el-link>
                </div>
            </div>
        </template>
        <div class="el-margin-lg-top" slot="empty">
            <img src="/dashboard-panel/img/empty.svg" alt="empty">
            <p class="el-text-muted">Нет данных</p>
        </div>
    </el-tree>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/catalog.js') }}"></script>
@endsection
