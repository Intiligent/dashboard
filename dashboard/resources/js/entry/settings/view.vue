<template lang="html">
    <div class="el-grid">
        <div class="el-width-expand">
            <h2 class="el-text--bold">Settings</h2>
        </div>
        <div class="">
            <el-button type="primary" @click="addSettingGroup">
                <i class="el-icon-folder-plus el-icon--left"></i>Add settings group
            </el-button>
        </div>
    </div>

    <el-tabs v-model="currentGroup" tab-position="left" style="margin-top: 30px;">
        <el-tab-pane
            v-for="group, index in tree"
            :label="group.title"
            :key="group.key.toLowerCase()"
            :name="group.key.toLowerCase()"
        >
            <template #label>
                <i class="el-icon--left" :class="group.icon"></i>
                <span class="el-text--truncate" v-text="group.title"></span>
            </template>
            <h2 class="el-text--bold el-margin-sm-bottom" v-text="group.title"></h2>
            <div class="el-text--muted" v-text="group.description"></div>
            <div class="el-grid el-margin el-margin-sm-top">
                <div class="" v-if="group.children && group.children.length">
                    <el-link type="primary" @click.prevent="onSettingItemAdd(group)">
                        <i class="el-icon-plus22 el-icon--left"></i>Add setting
                    </el-link>
                </div>
                <div class="">
                    <el-link type="primary" @click.prevent="onSettingGroupEdit(group)">
                        <i class="el-icon-pencil el-icon--left"></i>Edit group
                    </el-link>
                </div>
            </div>
            <form @submit.prevent="onSubmit(group)" v-if="group.children && group.children.length">
                <section class="el-margin" v-for="setting in group.children" :key="setting.id">
                    <div class="el-grid el-grid-sm el-flex-middle el-margin-xs">
                        <div class="el-text--bold" v-text="setting.title"></div>
                        <div class="el-width-expand">
                            <el-tooltip content="Copy to clipboard" placement="top">
                                <el-tag
                                    type="success"
                                    effect="plain"
                                    size="small"
                                    class="clipboard-copy"
                                    style="cursor: pointer;"
                                    v-text="setting.key"
                                    :data-clipboard-text="setting.key"
                                ></el-tag>
                            </el-tooltip>
                        </div>
                        <div class="">
                            <el-link
                                type="primary"
                                class="el-float--right"
                                @click.prevent="onSettingItemModify(setting)"
                                v-if="!setting.is_system1"
                            >Edit</el-link>
                        </div>
                    </div>
                    <component-interface :model="setting"></component-interface>
                    <div class="el-margin-xs-top el-text--muted el-text--small" v-text="setting.description" v-if="setting.description"></div>
                </section>
                <div class="el-text--right">
                    <el-button
                        type="primary"
                        native-type="submit"
                        :loading="state.isLoading"
                        :disabled="state.isLoading"
                    >
                        <template #icon>
                            <i class="el-icon-paperplane"></i>
                        </template>Save
                    </el-button>
                </div>
            </form>
            <el-empty description="Settings group is empty" v-else>
                <el-button @click="onSettingItemAdd(group)">
                    <template #icon>
                        <i class="el-icon-plus2"></i>
                    </template>
                    Add new setting
                </el-button>
            </el-empty>
        </el-tab-pane>
    </el-tabs>
</template>

<script>
import { h, inject, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Clipboard from 'clipboard';
import ComponentInterface from './components/interface';
import ModalSetting from '@dashboard/components/modal/modal.setting.vue';
import {
    ElButton,
    ElEmpty,
    ElLink,
    ElMessage,
    ElMessageBox,
    ElTabs,
    ElTabPane,
    ElTag,
    ElTooltip,
} from 'element-plus';
import { putSettings, deleteSetting } from '@dashboard/service/request/settings';

export default {
    components: {
        ElButton,
        ElEmpty,
        ElLink,
        ElMessageBox,
        ElTabs,
        ElTabPane,
        ElTag,
        ElTooltip,
        ComponentInterface,
    },

    watch: {
        currentGroup: function(value) {
            this.router.push({ path: location.pathname, hash: '#' + value.toLowerCase() });
        },
    },

    setup() {
        const route = useRoute();
        const currentTabName = route.hash ? route.hash.replace(/^#/, '') : window.app.settings[0].code;
        const currentGroup = ref(currentTabName);
        const routes = window.app.routes;
        const router = useRouter();
        const state = inject('state');
        const tree = ref(window.app.settings);

        const isRoot = function(model) {
            return !model.parent_id;
        };

        const onSubmit = function(group) {
            const payload = group.children.map(({ id, key, value }) => {
                return { id, key, value };
            });
            return putSettings(payload, {
                state: state.value,
                notify: true,
            });
        };

        const addSettingGroup = function() {
            return onSettingItemModify();
        };

        const onSettingGroupEdit = function(group) {
            return onSettingItemModify(group);
        };

        /**
         * Modify setting item/group
         *
         * @param {Object} model [Setting]
         * @return {Promise}
         */
        const onSettingItemModify = (model = {}) => {
            return ElMessageBox({
                title: 'Settings ' + (isRoot(model) ? 'group' : 'item'),
                message: h(ModalSetting, {
                    model,
                    tree: tree.value,
                    onCreate: function(data) {
                        if (isRoot(data)) {
                            return tree.value.push(data);
                        }
                        const parent = tree.value.find((group) => {
                            return group.id === data.parent_id;
                        });
                        if (!parent) {
                            throw new Error(`Not found group with id=${data.parent_id}`);
                        }
                        if (!parent.children) {
                            parent.children = [];
                        }
                        parent.children.push(data);
                    },
                    onUpdate: function(data) {
                        Object.assign(model, data)
                    },
                    onDelete: function() {
                        removeItem(model).then((deleted) => {
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
         * Remove setting item
         *
         * @param {Object} model [setting item model]
         * @return {Promise(Boolean)}
         */
        const removeItem = async (model) => {
            if (!confirm('Are you sure you want delete setting item?')) {
                return false;
            }
            const payload = { id: model.id };
            try {
                const response = await deleteSetting(payload, {
                    state: state.value,
                    notify: true,
                });
                if (response.error === 200) {
                    if (model.parent_id) {
                        const group = tree.value.find((group) => {
                            return group.id === model.parent_id;
                        });
                        const index = group.children.findIndex((item) => {
                            return item.id === model.id;
                        });
                        group.children.splice(index, 1);
                    } else {
                        const groupIndex = tree.value.findIndex((group) => {
                            return group.id === model.id;
                        });
                        if (groupIndex < 0) {
                            throw new Error(`Group with id=${model.id} not found`);
                        }
                        tree.value.splice(groupIndex, 1);
                    }
                    currentGroup.value = tree.value[0].key.toLowerCase();
                    return true;
                }
            } catch (error) {
                console.log('error', error);
                return false;
            }
        };

        /**
         * Add setting item to group
         *
         * @param {Object} group [Setting group model]
         * @return {Promise}
         */
        const onSettingItemAdd = (group) => {
            const model = { parent_id: group.id };
            return onSettingItemModify(model);
        };

        return {
            addSettingGroup,
            currentGroup,
            onSettingGroupEdit,
            onSettingItemAdd,
            onSettingItemModify,
            onSubmit,
            removeItem,
            state,
            router,
            tree,
        };
    },

    mounted: function() {
        this.clipboard = new Clipboard('.clipboard-copy');
        this.clipboard.on('success', function(event) {
            ElMessage({
                message: 'Success copy data to clipboard',
                type: 'success',
            });
        });
    },
}
</script>

<style lang="scss">
@use '~element-plus/theme-chalk/src/mixins/function' as *;
@use '../../../style/theme/common';

@use '~element-plus/theme-chalk/src/tabs';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/tag';
@use '~element-plus/theme-chalk/src/empty';
@use '~element-plus/theme-chalk/src/tooltip';
@use '~element-plus/theme-chalk/src/message';

.el-tabs__nav {
    &.is-left {
        min-width: 220px;
        max-width: 240px;
        .el-tabs__item {
            border-top-left-radius: getCssVar('border-radius', 'base');
            border-bottom-left-radius: getCssVar('border-radius', 'base');

            &.is-active {
                background-color: var(--el-color-primary-light-9);
            }
        }
    }
}

.el-tag {
    &.el-tag--success {
        transition: var(--el-transition-duration-fast);
        &:hover {
            background-color: var(--el-color-success-light-5);
            color: #326718;
        }
    }
}
</style>
