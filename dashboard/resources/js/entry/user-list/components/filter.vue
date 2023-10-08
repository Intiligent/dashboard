<template lang="html">
    <el-drawer
        title="Filter users"
        size="360px"
        v-model="modelValue"
    >
        <el-form
            id="form-fliter-user"
            label-position="top"
            @submit.prevent="onSubmit"
        >
            <el-form-item label="Name">
                <el-input
                    name="name"
                    size="large"
                    placeholder="Input user name"
                    v-model="form.name"
                    clearable
                >
                    <template #prefix>
                        <i class="el-icon-users2"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="Email">
                <el-input
                    size="large"
                    placeholder="Input user email"
                    name="email"
                    v-model="form.email"
                    clearable
                >
                    <template #prefix>
                        <i class="el-icon-envelop2"></i>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item label="Created at">
                <el-date-picker
                    v-model="form.created_at"
                    name="created_at"
                    type="daterange"
                    size="large"
                    start-placeholder="Start date"
                    end-placeholder="End date"
                    value-format="YYYY-MM-DD"
                    clearable
                ></el-date-picker>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button
                native-type="submit"
                size="large"
                form="form-fliter-user"
                type="primary"
            >
                <i class="el-icon-paperplane el-icon--left"></i>Submit
            </el-button>
        </template>
    </el-drawer>
</template>

<script>
import { reactive, watch } from 'vue';
import {
    ElButton,
    ElDatePicker,
    ElDrawer,
    ElForm,
    ElFormItem,
    ElInput,
} from 'element-plus';

export default {
    components: {
        ElButton,
        ElDatePicker,
        ElDrawer,
        ElForm,
        ElFormItem,
        ElInput,
    },

    props: {
        modelValue: {
            type: Boolean,
        },
    },

    emits: ['update:modelValue', 'submit'],

    setup(props, { emit }) {
        const form = reactive({
            name: null,
            email: null,
            created_at: null,
        });

        const onSubmit = function() {
            emit('submit', form);
        };

        watch(() => props.modelValue, (value) => {
            emit('update:modelValue', value);
        });

        return {
            form,
            onSubmit,
        };
    }
}
</script>

<style lang="scss">
@use '../../../../style/theme/common';

@use '~element-plus/theme-chalk/src/drawer';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/date-picker';
</style>
