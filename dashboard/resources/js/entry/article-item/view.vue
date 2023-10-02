<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="el-width-expand">
            <h2 class="el-text--bold">Article</h2>
        </div>
        <div class="">
            <el-button
                tag="a"
                :href="route('dashboard.articleList')"
            >
                <i class="el-icon-arrow-left8 el-icon--left"></i>Article list
            </el-button>
            <el-button
                type="primary"
                tag="a"
                :href="route('dashboard.article', {id: 'new'})"
            >
                <i class="el-icon-plus el-icon--left"></i>Add new article
            </el-button>
        </div>
    </div>

    <el-form
        style="padding-top: 30px;"
        label-width="120px"
        require-asterisk-position="right"
        @submit.prevent="onSubmit"
    >
        <el-form-item label="Title" prop="title">
            <el-input
                name="name"
                size="large"
                placeholder="Input article title"
                v-model="form.title"
            >
                <template #prefix>
                    <i class="el-icon-users2"></i>
                </template>
            </el-input>
        </el-form-item>
        <el-form-item label="Language" prop="lang_id">
            <el-select
                name="lang_id"
                size="large"
                placeholder="Select article language"
                v-model="form.lang_id"
            >
                <el-option
                    v-for="language in languages"
                    :label="language.icon + ' ' + language.name"
                    :value="language.id"
                ></el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="Slug" prop="slug">
            <el-input size="large" placeholder="Input article slug" name="slug" v-model="form.slug">
                <template #prefix>
                    <i class="el-icon-code"></i>
                </template>
            </el-input>
        </el-form-item>
        <div class="el-form-item">
            <el-input type="textarea" size="large" placeholder="Input article text" name="text" v-model="form.text"></el-input>
        </div>
        <el-form-item label="Created/Updated" v-if="form.id">
            <span v-text="form.created_at"></span>
            <span style="padding: 0 5px;">/</span>
            <span v-text="form.updated_at"></span>
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
import { computed, inject, reactive, readonly, ref } from 'vue';
import {
    ElButton,
    ElForm,
    ElFormItem,
    ElInput,
    ElOption,
    ElSelect,
} from 'element-plus';
import { postArticle } from '@dashboard/service/request/article';

export default {
    components: {
        ElButton,
        ElForm,
        ElFormItem,
        ElInput,
        ElOption,
        ElSelect,
    },

    inject: ['route'],

    setup() {
        const languages = readonly(window.app.languages);
        const model = window.app.model;
        const form = reactive(Object.assign({
            name: null,
            email: null,
            created_at: null,
        }, model));
        const state = inject('state');

        const onSubmit = function() {
            const payload = form;
            console.log('payload', payload);
            return postArticle(payload, {
                state: state.value,
                notify: true,
            }).then((response) => {
                console.log('[postArticle response]', response);
            });
        };

        return {
            form,
            languages,
            model,
            onSubmit,
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
@use '~element-plus/theme-chalk/src/select';
@use '~element-plus/theme-chalk/src/option';
@use '~element-plus/theme-chalk/src/popper';
</style>
