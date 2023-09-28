<template lang="html">
    <el-drawer
        title="Filter articles"
        size="360px"
        v-model="modelValue"
    >
        <el-form
            id="form-fliter"
            label-position="top"
            @submit.prevent="onSubmit"
        >
            <el-form-item label="Title" prop="title">
                <el-input
                    size="large"
                    placeholder="Input article title"
                    v-model="form.title"
                    clearable
                >
                    <template #prefix>
                        <i class="el-icon-pilcrow"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="Language" prop="lang_id">
                <el-select
                    name="lang_id"
                    size="large"
                    class="el-width-1-1"
                    placeholder="Select article language"
                    v-model="form.lang_id"
                    clearable
                    multiple
                >
                    <template #prefix>
                        <i class="el-icon-sphere"></i>
                    </template>
                    <el-option
                        v-for="language in languages"
                        :label="language.icon + ' ' + language.name"
                        :value="language.id"
                    ></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="Created at" prop="created_at">
                <el-date-picker
                    type="daterange"
                    size="large"
                    start-placeholder="Start date"
                    end-placeholder="End date"
                    value-format="YYYY-MM-DD"
                    v-model="form.created_at"
                    clearable
                ></el-date-picker>
            </el-form-item>
            <el-form-item label="Active" prop="active">
                <el-checkbox-group
                    size="large"
                    v-model="form.active"
                >
                    <el-checkbox :key="1" :label="1" border>Active</el-checkbox>
                    <el-checkbox :key="0" :label="0" border>Not active</el-checkbox>
                </el-checkbox-group>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button
                native-type="submit"
                size="large"
                form="form-fliter"
                type="primary"
            >
                <i class="el-icon-paperplane el-icon--left"></i>Submit
            </el-button>
        </template>
    </el-drawer>
</template>

<script>
import { reactive } from 'vue';
import {
    ElButton,
    ElCheckbox,
    ElCheckboxGroup,
    ElDatePicker,
    ElDrawer,
    ElForm,
    ElFormItem,
    ElInput,
    ElOption,
    ElSelect,
} from 'element-plus';

export default {
    components: {
        ElButton,
        ElCheckbox,
        ElCheckboxGroup,
        ElDatePicker,
        ElDrawer,
        ElForm,
        ElFormItem,
        ElInput,
        ElOption,
        ElSelect,
    },

    props: {
        modelValue: {
            type: Boolean,
        },
        languages: {
            type: Array,
        },
    },

    emits: ['update:modelValue', 'submit'],

    watch: {
        modelValue: function(value, oldValue) {
            this.$emit('update:modelValue', value);
        },
    },

    setup(props, { emit }) {
        const form = reactive({
            active: [1],
        });
        const onSubmit = function() {
            emit('submit', form);
        };

        return {
            form,
            onSubmit,
        };
    },
}
</script>

<style lang="scss">
@use '../../../../style/theme/common';
@use '~element-plus/theme-chalk/src/drawer';
@use '~element-plus/theme-chalk/src/date-picker';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/option';
@use '~element-plus/theme-chalk/src/select';
@use '~element-plus/theme-chalk/src/tag';
@use '~element-plus/theme-chalk/src/popper';
@use '~element-plus/theme-chalk/src/checkbox';
@use '~element-plus/theme-chalk/src/checkbox-group';
</style>
