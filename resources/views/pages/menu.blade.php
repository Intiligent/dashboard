@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/menu.css') }}">
@endsection

@section('content')
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Меню</h2>
    </div>
    <div class="el-width-auto@md">
        <el-dropdown trigger="click" @command="handleMenuCommand">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item icon="el-icon-plus2" command="addgroup">Добавить группу</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<el-tabs class="el-margin" tab-position="left" v-model="currentTabName" v-if="tree.length" v-cloak>
    <el-tab-pane v-for="item in tree" :key="item.id" :name="item.code">
        <span slot="label">
            <i class="el-icon-date"></i>
            <span v-text="item.name"></span>
        </span>
        <div class="">
            <div class="el-grid el-margin-bottom el-flex el-flex-middle">
                <div class="el-width-expand">
                    <h3 class="el-text-bold" v-text="item.name"></h3>
                </div>
                <div class="el-width-auto">
                    <div class="el-grid">
                        <div class="">
                            <el-tag size="mini" v-text="item.code"></el-tag>
                        </div>
                        <div class="">
                            <el-link type="primary" @click.prevent="syncMenu({parent: item})" icon="el-icon-plus22">Добавить</el-link>
                        </div>
                        <div class="">
                            <el-link type="primary" @click.prevent="syncMenuGroup({model: item})" icon="el-icon-pencil6">Редактировать группу</el-link>
                        </div>
                    </div>
                </div>
            </div>
            <el-tree :data="item.children" :props="{label: 'name'}" node-key="id" draggable default-expand-all :filter-node-method="onFilterNode" @node-drop="onNodeDrop" :ref="item.code">
                <template v-slot="{node, data: row}">
                    <div class="el-grid el-grid-sm el-flex el-flex-middle el-tree-node-container">
                        <div class="el-width-2-5 el-text-truncate" :title="row.name">
                            <small v-if="row.icon" class="el-text-muted" style="margin-right: 5px;"><i :class="row.icon"></i></small>
                            <a href="#" v-text="row.name" @click.prevent.stop="syncMenu({model: row, node})"></a>
                        </div>
                        <div class="el-width-expand">
                            <i class="el-icon-code el-margin-sm-right el-text-sm" v-if="row.attribute" :title="row.attribute"></i><span v-text="row.value"></span>
                        </div>
                        <div class="el-width-auto">
                            <el-tooltip content="Добавить категорию" placement="top">
                                <el-button type="primary" size="mini" icon="el-icon-plus2" plain narrow @click.prevent.stop="onAppendNode(row, node)"></el-button>
                            </el-tooltip>
                        </div>
                        <div class="el-width-auto">
                            <el-switch v-model="row.active" active-color="#13ce66" @click.native.stop @change="onActiveChange(row, $event)"></el-switch>
                        </div>
                        <div class="el-width-auto">
                            <el-button size="mini" type="danger" circle icon="el-icon-cross2" @click="onDestroy(row, node)"></el-button>
                        </div>
                    </div>
                </template>
                <div class="el-margin-lg-top" slot="empty">
                    <div class="blank-result">Нет данных</div>
                </div>
            </el-tree>
        </div>
    </el-tab-pane>
    <div class="el-margin" slot="header">
        <el-button type="warning" icon="el-icon-plus22" class="el-width-1-1" @click="syncMenuGroup()" plain>Добавить группу</el-button>
    </div>
</el-tabs>
<div class="blank-result" v-else>
    <p class="">Нет данных</p>
    <div class=""><el-button type="warning" icon="el-icon-plus22" @click="syncMenuGroup()" plain>Добавить группу</el-button></div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/menu.js') }}"></script>
@endsection
