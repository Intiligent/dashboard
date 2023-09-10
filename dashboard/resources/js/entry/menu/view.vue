<template lang="html">
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
                        <el-dropdown-item command="addMenuGroup">
                            <i class="el-icon-folder-plus4 el-icon--left"></i>Add group
                        </el-dropdown-item>
                        <el-dropdown-item command="editGroup">
                            <i class="el-icon-pencil6 el-icon--left"></i>Edit group
                        </el-dropdown-item>
                        <el-dropdown-item command="addMenuItem">
                            <i class="el-icon-plus2 el-icon--left"></i>Add menu item
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
                ref="treeRef"
                :allow-drop="allowTreeDrop"
                :allow-drag="allowTreeDrag"
                :data="group.children"
                :filter-node-method="onNodeFilter"
                :name="group.name"
                draggable
                default-expand-all
                node-key="id"
                @node-drop="onNodeDrop"
            >
                <template #default="{ node, data }">
                    <div class="el-grid el-grid-sm el-width-1-1 el-flex-middle">
                        <div class="el-width-expand">
                            <i v-if="data.icon" class="el-icon--left" :class="data.icon"></i>
                            <el-link @click.prevent.stop="onMenuItemModify(data, node)">
                                {{ data.name }}
                            </el-link>
                        </div>
                        <div>
                            <span class="el-text--muted" v-text="data.value"></span>
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
                                :loading="isItemActiveLoading[data.id]"
                                :before-change="() => onMenuItemActiveChange(data)"
                                @click.stop=""
                            />
                        </div>
                        <div class="">
                            <el-tooltip content="Delete menu item" placement="top">
                                <el-button
                                    type="danger"
                                    size="small"
                                    circle
                                    @click.stop="removeMenuItem(data, node)"
                                ><i class="el-icon-cross"></i></el-button>
                            </el-tooltip>
                        </div>
                    </div>
                </template>
                <template #empty>
                    <el-empty :description="'No menu items for ' + group.name">
                        <el-button
                            type="primary"
                            @click="addMenuItem"
                        ><i class="el-icon-plus2 el-icon--left"></i> Add new menu item</el-button>
                    </el-empty>
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
</template>

<script>
import { h, inject, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ModalMenuItem from '@dashboard/components/modal/modal.menu-item.vue';
import {
    ElButton,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElEmpty,
    ElInput,
    ElForm,
    ElFormItem,
    ElLink,
    ElMessageBox,
    ElSwitch,
    ElTabs,
    ElTabPane,
    ElTooltip,
    ElTree,
} from 'element-plus';
import { postMenu, deleteMenu } from '@dashboard/service/request/menu';

export default {
    components: {
        ElButton,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElEmpty,
        ElInput,
        ElForm,
        ElFormItem,
        ElLink,
        ElMessageBox,
        ElSwitch,
        ElTabs,
        ElTabPane,
        ElTooltip,
        ElTree,
    },

    watch: {
        currentMenuGroup: function(value) {
            this.router.push({ path: location.pathname, hash: '#' + value });
        },
        searchQuery: function(value) {
            for (const tree of this.treeRef) {
                tree.filter(value);
            }
        },
    },

    setup() {
        const route = useRoute();
        const currentTabName = route.hash ? route.hash.replace(/^#/, '') : window.app.tree[0].code;
        const currentMenuGroup = ref(currentTabName);
        const isItemActiveLoading = ref({});
        const routes = window.app.routes;
        const router = useRouter();
        const searchQuery = inject('searchQuery');
        const tree = ref(window.app.tree);
        const treeRef = ref(null);

        const handleMenuCommand = (command) => {
            if (command === 'addMenuItem') {
                addMenuItem();
            }
            if (command === 'addMenuGroup') {
                addMenuGroup();
            }
            if (command === 'editGroup') {
                editGroup();
            }
        };

        const isRoot = function(model) {
            return !model.parent_id;
        };

        const addMenuItem = function() {
            const parent = tree.value.find((item) => {
                return item.code === currentMenuGroup.value;
            });
            const model = { parent_id: parent.id };
            return onMenuItemModify(model, parent);
        };

        const addMenuGroup = function() {
            return onMenuItemModify({}, {});
        };

        const editGroup = function() {
            const model = tree.value.find((item) => {
                return item.code === currentMenuGroup.value;
            });
            return onMenuItemModify(model, {});
        };

        /**
         * Add or update menu item/group
         *
         * @param {Object} model [ModelMenu]
         * @param {Node} node [Node tree]
         * @return {Promise}
         */
        const onMenuItemModify = (model, node) => {
            return ElMessageBox({
                title: 'Menu ' + (isRoot(model) ? 'group' : 'item'),
                message: h(ModalMenuItem, {
                    model,
                    routes,
                    tree: tree.value,
                    onCreate: function(data) {
                        if (isRoot(data)) {
                            return tree.value.push(data);
                        }
                        if (!node.children) {
                            node.children = [];
                        }
                        node.children.push(data);
                    },
                    onUpdate: function(data) {
                        Object.assign(model, data)
                    },
                    onDelete: function() {
                        removeMenuItem(model, node).then((deleted) => {
                            deleted && ElMessageBox.close();
                        });
                    },
                }),
                showConfirmButton: false,
                customClass: 'el-message--sm',
            }).catch((error) => {
                console.log(error);
            });
        };

        /**
         * Remove menu item
         *
         * @param {Object} model [menu item model]
         * @param {TreeNode} node [tree node]
         * @return {Promise(Boolean)}
         */
        const removeMenuItem = async (model, node) => {
            if (!confirm('Are you sure you want delete menu item?')) {
                return false;
            }
            const payload = { id: model.id };
            try {
                const response = await deleteMenu(payload);
                if (response.error === 200) {
                    const parent = node.parent;
                    const children = parent.data.children || parent.data;
                    const index = children.findIndex((item) => item.id === model.id);
                    children.splice(index, 1);
                    tree.value = [...tree.value];
                    return true;
                }
            } catch (error) {
                console.log('error', error);
                return false;
            }
        };

        /**
         * [onChildMenuItemAdd description]
         *
         * @param  {[type]} parent               [description]
         * @return {[type]}        [description]
         */
        const onChildMenuItemAdd = (parent) => {
            const model = { parent_id: parent.id };
            return onMenuItemModify(model, parent);
        };

        const onMenuItemActiveChange = async (model) => {
            const payload = { id: model.id, active: model.active };
            isItemActiveLoading.value[model.id] = true;
            try {
                const response = await postMenu(payload);
                if (response.error === 200) {
                    return true;
                }
            } finally {
                delete isItemActiveLoading.value[model.id];
            }
        };

        const onMenuItemDelete = (model) => {
            console.log('[model1]', model);
        };

        const onNodeFilter = (value, data) => {
            if (!value) {
                return true;
            }
            return data.name.toLowerCase().includes(value.toLowerCase());
        };

        const allowTreeDrop = (draggingNode, dropNode, type) => {
            // console.log('[allowTreeDrop]', 'draggingNode', draggingNode, 'dropNode', dropNode, 'type', type);
            // if (dropNode.data.label === 'Level two 3-1') {
            //     return type !== 'inner';
            // }
            return true;
        };

        const allowTreeDrag = (draggingNode) => {
            // console.log('[allowTreeDrag]', draggingNode);
            // return !draggingNode.data.label.includes('Level three 3-1-1');
            return true;
        };

        /**
         * [description]
         * @param  {[type]} nodeDragging               [description]
         * @param  {[type]} nodeDrop                   [description]
         * @param {String} eventType [after|before|inner]
         * @param  {[type]} event                      [description]
         * @return {[type]}              [description]
         */
        const onNodeDrop = function(nodeDragging, nodeDrop, eventType, event) {
            console.log('nodeDragging:', nodeDragging, 'nodeDrop:', nodeDrop, 'eventType', eventType, 'DgarEvent', event);
        };

        return {
            allowTreeDrop,
            allowTreeDrag,
            addMenuItem,
            currentMenuGroup,
            handleMenuCommand,
            isItemActiveLoading,
            onChildMenuItemAdd,
            onMenuItemActiveChange,
            onMenuItemDelete,
            onMenuItemModify,
            onNodeDrop,
            onNodeFilter,
            removeMenuItem,
            searchQuery,
            router,
            tree,
            treeRef,
        };
    },
}
</script>

<style lang="scss">
</style>
