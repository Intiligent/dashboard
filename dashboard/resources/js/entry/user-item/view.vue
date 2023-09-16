<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="el-width-expand">
            <h2 class="el-text--bold">User</h2>
        </div>
        <div class="">
            <el-button
                tag="a"
                :href="route('dashboard.userList')"
            >
                <i class="el-icon-arrow-left8 el-icon--left"></i>User list
            </el-button>
            <el-button
                type="primary"
                tag="a"
                :href="route('dashboard.user', {id: 'new'})"
            >
                <i class="el-icon-user-plus el-icon--left"></i>Add new user
            </el-button>
        </div>
    </div>

    <el-form
        class="pl-form-user"
        label-width="120px"
        require-asterisk-position="right"
        @submit.prevent="onSubmit"
    >
        <el-form-item>
            <div class="el-grid" style="line-height: 1;">
                <div class="">
                    <el-tooltip placement="left" content="Upload avatar">
                        <el-upload
                            :http-request="postUserAvatarRequest"
                            :accept="acceptFileTypes"
                            :show-file-list="false"
                            :on-success="handleAvatarSuccess"
                            :on-error="handleAvatarError"
                            :on-change="handleAvatarChange"
                            :on-exceed="handleAvatarExceed"
                        >
                            <el-avatar size="large" :src="form.avatar" />
                        </el-upload>
                    </el-tooltip>
                </div>
                <div class="">
                    <div class="pl-user--name" v-text="model.name"></div>
                    <div class="pl-user--email" v-text="model.email"></div>
                </div>
            </div>
        </el-form-item>
        <el-form-item label="Name" prop="name">
            <el-input
                name="name"
                size="large"
                placeholder="Input user name"
                v-model="form.name"
            >
                <template #prefix>
                    <i class="el-icon-users2"></i>
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="Email" prop="email">
            <el-input size="large" placeholder="Input user email" name="email" v-model="form.email">
                <template #prefix>
                    <i class="el-icon-envelop2"></i>
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="Password" prop="password">
            <el-input
                size="large"
                type="password"
                name="password"
                placeholder="Input user password"
                v-model="form.password"
            >
                <template #prefix>
                    <i class="el-icon-lock"></i>
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="Created/Updated" v-if="form.id">
            <pre style="margin: 0;" v-text="form.created_at + ' / ' + form.updated_at"></pre>
        </el-form-item>
        <el-form-item>
            <div class="el-width-1-1 el-text--right">
                <el-button
                    type="primary"
                    native-type="submit"
                    size="large"
                    style="width: 200px;"
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
    ElAvatar,
    ElButton,
    ElForm,
    ElFormItem,
    ElInput,
    ElTooltip,
    ElUpload,
} from 'element-plus';
import { postUser, postUserAvatar } from '@dashboard/service/request/user';

export default {
    components: {
        ElAvatar,
        ElButton,
        ElForm,
        ElFormItem,
        ElInput,
        ElTooltip,
        ElUpload,
    },

    inject: ['route'],

    setup() {
        const model = window.app.model;
        const form = reactive(Object.assign({
            name: null,
            email: null,
            created_at: null,
        }, model));
        const state = inject('state');

        /**
         * Upload user avatar
         *
         * @param {Object} options [{action: String, data: Object, file: File, filename: String, headers: Object, method: String, onError: Function, onProgress: Function, onSuccess: Function, withCredentials: Boolean}]
         * @return {void}
         */
        const postUserAvatarRequest = function(options) {
            const formData = new FormData();
            formData.append(options.filename, options.file);
            formData.append('id', form.id);

            postUserAvatar(formData, {
                state: state.value,
                notify: true,
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then((response) => {
                if (response.error === 200) {
                    form.avatar = response.data.path;
                }
            });
        };

        const handleAvatarSuccess = function(response, file) {
            console.log('[handleAvatarSuccess]', response, file);
        };

        const handleAvatarError = function(error, file, files) {
            console.log('[handleAvatarError]', error, file, files);
        };

        const handleAvatarChange = function(file, files) {
            console.log('[handleAvatarChange]', file, files);
        };

        const handleAvatarExceed = function(file, files) {
            console.log('[handleAvatarExceed]', file, files);
        };

        const acceptFileTypes = computed(() => {
            return [
                // 'image/png', 'image/gif', 'image/jpeg',
                'image/*',
            ].join(', ');
        });

        const onSubmit = function() {
            const payload = form;
            console.log('payload', payload);
            return postUser(payload, {
                state: state.value,
                notify: true,
            }).then((response) => {
                console.log('[postUser response]', response);
            });
        };

        return {
            acceptFileTypes,
            form,
            handleAvatarError,
            handleAvatarChange,
            handleAvatarSuccess,
            handleAvatarExceed,
            model,
            onSubmit,
            postUserAvatarRequest,
            state,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/avatar';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/upload';
@use '~element-plus/theme-chalk/src/tooltip';

.pl-form-user {
    padding-top: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
.pl-user--name {
    font-size: 24px;
    font-weight: bold;
    line-height: 32px;
}
.pl-user--email {
    font-size: 16px;
    line-height: 24px;
    color: var(--el-text-color-secondary);
}
</style>
