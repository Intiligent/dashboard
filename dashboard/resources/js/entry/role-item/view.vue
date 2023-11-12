<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="el-width-expand">
            <h2 class="el-text--bold">Role</h2>
        </div>
        <div class="">
            <el-button
                tag="a"
                :href="route('dashboard.roleList')"
            >
                <i class="el-icon-arrow-left8 el-icon--left"></i>Role list
            </el-button>
            <el-button
                type="primary"
                tag="a"
                :href="route('dashboard.role', {id: 'new'})"
            >
                <i class="el-icon-user-plus el-icon--left"></i>Add new role
            </el-button>
        </div>
    </div>

    <el-form
        class="el-margin-md"
        label-width="120px"
        require-asterisk-position="right"
        @submit.prevent="onSubmit"
    >
        <el-form-item label="ID" v-if="form.id">
            <pre style="margin: 0;" v-text="form.id"></pre>
        </el-form-item>
        <el-form-item label="Name" prop="name">
            <el-input
                name="name"
                size="large"
                placeholder="Role name (manager)"
                v-model="form.name"
            >
                <template #prefix>
                    <i class="el-icon-users2"></i>
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="Permissions" prop="assign_permissions">
            <el-checkbox-group
                class="el-margin-xs-bottom"
                v-model="form.assign_permissions"
            >
                <el-checkbox
                    v-for="permission in filteredPermissions"
                    :label="permission.id"
                    :key="permission.id"
                >
                    <div class="name" v-text="permission.name"></div>
                    <div class="description" v-if="permission.description" v-text="permission.description"></div>
                    <el-tag size="small" :type="permission.guard_name === 'web' ? 'warning' : ''" v-text="permission.guard_name"></el-tag>
                </el-checkbox>
            </el-checkbox-group>
            <a :href="route('dashboard.permission', {id: 'new'})">
                <i class="el-icon-plus2 el-margin-xs-right"></i>Add new permission
            </a>
        </el-form-item>
        <el-form-item label="Created/Updated" v-if="form.id">
            <pre style="margin: 0;" v-text="form.created_at + ' / ' + form.updated_at"></pre>
        </el-form-item>
        <el-form-item>
            <div class="el-width-1-1 el-text--right">
                <el-button
                    type="primary"
                    native-type="submit"
                    :loading="state.isLoading"
                    :disabled="state.isLoading"
                >
                    <template #icon>
                        <i class="el-icon-paperplane el-icon--left"></i>
                    </template>
                    Submit
                </el-button>
            </div>
        </el-form-item>
    </el-form>
</template>

<script>
import { computed, inject, reactive, ref } from 'vue';
import {
    ElButton,
    ElCheckbox,
    ElCheckboxGroup,
    ElForm,
    ElFormItem,
    ElInput,
    ElLink,
    ElTag,
} from 'element-plus';
import {
    postRole,
    deleteRole,
} from '@dashboard/service/request/role';

export default {
    components: {
        ElButton,
        ElCheckbox,
        ElCheckboxGroup,
        ElForm,
        ElFormItem,
        ElInput,
        ElLink,
        ElTag,
    },

    inject: ['route'],

    setup() {
        const model = window.app.role;
        const form = reactive(Object.assign({
            name: null,
            assign_permissions: [],
            created_at: null,
        }, model));
        const permissions = ref(window.app.permissions);
        const searchQuery = inject('searchQuery');
        const state = inject('state');

        const filteredPermissions = computed(() => {
            if (!searchQuery.value) {
                return permissions.value;
            }
            return permissions.value.filter((row) => {
                return row.name.includes(searchQuery.value) || (row.description || '').includes(searchQuery.value);
            });
        });

        const onSubmit = function() {
            const payload = form;
            return postRole(payload, {
                state: state.value,
                notify: true,
            }).then((response) => {
                console.log('[postRole response]', response);
            });
        };

        const onRoleDelete = function() {
            if (!confirm('Are you sure you want delete role?')) {
                return;
            }
            const payload = { id: model.id };
            return deleteRole(payload, {
                state: state.value,
                notify: true,
            }).then((response) => {
                console.log('[deleteRole response]', response);
            });
        };

        return {
            form,
            model,
            onRoleDelete,
            onSubmit,
            filteredPermissions,
            state,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/checkbox';
@use '~element-plus/theme-chalk/src/checkbox-group';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/tag';

.el-checkbox-group {
    width: 100%;
    border: var(--el-border-width) var(--el-border-style) var(--el-border-color);
    border-radius: var(--el-border-radius-base);
    overflow: hidden;
    .el-checkbox {
        display: block;
        height: auto;
        min-height: var(--el-checkbox-height, 16px);
        position: relative;
        margin: 0;
        padding: 12px;
        &:hover {
            background-color: var(--el-color-info-light-9);
        }
        .el-checkbox__input {
            vertical-align: top;
        }
        .el-checkbox__label {
            .name {
                font-weight: bold;
            }
            .description {
                color: var(--el-text-color-secondary);
                font-size: var(--el-font-size-small);
                padding-top: 5px;
            }
        }
        .el-tag {
            position: absolute;
            right: 12px;
            top: 9px;
        }
        & + .el-checkbox {
            border-top: var(--el-border-width) var(--el-border-style) var(--el-border-color);
        }
    }
}
</style>
