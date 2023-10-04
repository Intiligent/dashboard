<template lang="html">
    <div class="el-grid el-grid-sm">
        <div class="el-width-expand">
            <h2 class="el-text--bold">Menu</h2>
        </div>
        <div class="">
            <el-button type="primary" @click="addMenuItem">
                <template #icon>
                    <i class="el-icon-plus2"></i>
                </template>
                Add menu item
            </el-button>
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
                        <el-dropdown-item command="editGroup">
                            <i class="el-icon-pencil6 el-icon--left"></i>Edit group
                        </el-dropdown-item>
                        <el-dropdown-item command="addMenuGroup">
                            <i class="el-icon-folder-plus el-icon--left"></i>Add menu group
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
                            <el-link
                                :href="data.value === '#' ? '' : data.url"
                                target="_blank"
                                @click.stop=""
                            >
                                <i class="el-icon-new-tab2 el-icon--left" v-if="data.value !== '#'"></i>
                                {{ data.value }}
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
                        <el-button @click="addMenuItem">
                            <template #icon>
                                <i class="el-icon-plus2"></i>
                            </template>
                            Add new menu item
                        </el-button>
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
import { computed, h, inject, nextTick, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { isEmpty } from 'lodash';
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
            this.router.push({
                path: location.pathname,
                hash: '#' + value.toLowerCase(),
            });
        },
    },

    setup() {
        const route = useRoute();
        const currentTabName = computed(() => {
            if (route.hash) {
                return route.hash.replace(/^#/, '');
            }
            if (window.app.tree.length) {
                return window.app.tree[0].code.toLowerCase();
            }
        });
        const currentMenuGroup = ref(currentTabName.value);
        const currentMenuGroupIndex = computed(() => {
            return tree.value.findIndex((group) => {
                return group.code === currentMenuGroup.value;
            });
        });
        const isItemActiveLoading = ref({});
        const routes = window.app.routes;
        const router = useRouter();
        const searchQuery = inject('searchQuery');
        const tree = ref(window.app.tree);
        const treeRef = ref(null);
        const currentTreeRef = computed(() => {
            if (!treeRef.value) {
                return {};
            }
            return treeRef.value[currentMenuGroupIndex.value];
        });

        watch([searchQuery, currentTreeRef], ([newQuery, newTreeRef]) => {
            newTreeRef.filter(newQuery);
        });

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
                        const parentNode = currentTreeRef.value.getNode(data.parent_id);
                        if (parentNode) {
                            currentTreeRef.value.append(data, parentNode);
                        } else if (!parentNode && data.parent_id) {
                            const parent = tree.value.find((group) => {
                                return group.id === data.parent_id;
                            });
                            if (!parent.children) {
                                parent.children = [];
                            }
                            parent.children.push(data);
                        } else if (!data.parent_id) {
                            tree.value.push(data);
                            nextTick(() => {
                                currentMenuGroup.value = data.code.toLowerCase();
                            });
                        }
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
                    if (isEmpty(node) && !model.parent_id) {
                        nextTick(() => {
                            currentMenuGroup.value = tree.value.length && tree.value[0].code;
                        });
                        const index = tree.value.indexOf(model);
                        index >= 0 && tree.value.splice(index, 1);
                    } else {
                        currentTreeRef.value.remove(model);
                    }
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
            addMenuGroup,
            addMenuItem,
            currentMenuGroup,
            currentMenuGroupIndex,
            currentTreeRef,
            handleMenuCommand,
            isItemActiveLoading,
            onChildMenuItemAdd,
            onMenuItemActiveChange,
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
@use '~element-plus/theme-chalk/src/mixins/function' as *;
@use '../../../style/theme/common';

@use '~element-plus/theme-chalk/src/tabs';
@use '~element-plus/theme-chalk/src/dropdown';
@use '~element-plus/theme-chalk/src/popper';
@use '~element-plus/theme-chalk/src/tree';
@use '~element-plus/theme-chalk/src/switch';
@use '~element-plus/theme-chalk/src/empty';
@use '~element-plus/theme-chalk/src/tooltip';

.el-tabs__nav {
    &.is-left {
        min-width: 220px;
        .el-tabs__item {
            border-top-left-radius: getCssVar('border-radius', 'base');
            border-bottom-left-radius: getCssVar('border-radius', 'base');
            &.is-active {
                background-color: var(--el-color-primary-light-9);
            }
        }
    }
}

.el-tree {
    .el-tree-node__content {
        border-radius: getCssVar('border-radius', 'base');
        min-height: 38px;
        padding-right: 8px;
        border-bottom: 1px solid #f6f6f6;
        .el-grid {
            [class^=el-icon-], [class*=" el-icon-"] {
                vertical-align: text-bottom;
                opacity: 0.8;
            }
        }
    }
}
</style>
