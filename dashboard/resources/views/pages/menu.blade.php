@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard/style/menu.css') }}">
@endsection

@section('content')

<div class="el-grid">
    <div class="el-width-expand">
        <h2 class="el-text--bold">Menu</h2>
    </div>
    <div class="">
        <el-dropdown
            size="large"
            trigger="click"
            placement="bottom-end"
            @command="handleMenuCommand"
        >
            <el-button type="primary" class="is-narrow" plain>
                <i class="el-icon-menu"></i>
            </el-button>
            <template #dropdown>
                <el-dropdown-menu>
                    <el-dropdown-item>Action 1</el-dropdown-item>
                    <el-dropdown-item>Action 2</el-dropdown-item>
                    <el-dropdown-item>Action 3</el-dropdown-item>
                    <el-dropdown-item command="add">
                        <i class="el-icon-plus2 el-icon--left"></i>Add new menu
                    </el-dropdown-item>
                </el-dropdown-menu>
            </template>
        </el-dropdown>
    </div>
</div>
<el-tabs v-model="currentMenuGroup" tab-position="left" style="margin-top: 30px;">
    <el-tab-pane
        v-for="group, index in tree"
        :label="group.name"
        :key="group.name"
        :name="group.code"
    >
        <el-tree
            :allow-drop="allowTreeDrop"
            :allow-drag="allowTreeDrag"
            :data="group.children"
            :filter-node-method="onFilterNode"
            draggable
            default-expand-all
            node-key="id"
            :name="group.name"
            ref="treeRef"
        >
            <template #default="{ node, data }">
                <div class="el-grid el-grid-sm el-width-1-1 el-flex-middle">
                    <div class="el-width-expand">
                        <el-link @click.prevent.stop="onMenuItemModify(data)">
                            @{{ data.name }}
                        </el-link>
                    </div>
                    <div class="">
                        <el-tooltip content="Add child menu" placement="top">
                            <el-button
                                type="primary"
                                size="small"
                                class="is-narrow"
                                plain
                                @click.stop="onChildMenuItemAdd(data)"
                            ><i class="el-icon-plus2"></i></el-button>
                        </el-tooltip>
                    </div>
                    <div class="">
                        <el-switch
                            v-model="data.active"
                            style="--el-switch-on-color: #13ce66;"
                            :loading="isItemLoading[data.id]"
                            :before-change="() => onMenuItemActiveChange(data)"
                        />
                    </div>
                    <div class="">
                        <el-tooltip content="Delete menu item" placement="top">
                            <el-button
                                type="danger"
                                size="small"
                                circle
                                @click.stop="removeMenuItem(node, data)"
                            ><i class="el-icon-cross"></i></el-button>
                        </el-tooltip>
                    </div>
                </div>
            </template>
            <template #empty>
                <el-empty :description="'No menu items for ' + group.name"></el-empty>
            </template>
        </el-tree>
        <!-- @node-drag-start="handleDragStart"
        @node-drag-enter="handleDragEnter"
        @node-drag-leave="handleDragLeave"
        @node-drag-over="handleDragOver"
        @node-drag-end="handleDragEnd"
        @node-drop="handleDrop" -->
    </el-tab-pane>
</el-tabs>

@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard/js/menu.js') }}"></script>
@endsection
