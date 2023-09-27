<template lang="html">
<el-form
    label-position="left"
    require-asterisk-position="right"
    label-width="150px"
    ref="formRef"
    :model="innerModel"
    :rules="validationRules"
    @submit.prevent="onSubmit(formRef)"
>
    <el-form-item label="ID:" v-if="innerModel.id">
        <span v-text="innerModel.id"></span>
    </el-form-item>
    <el-form-item label="Key:" prop="key" for="setting.key" required>
        <el-input
            size="large"
            ref="settingKeyRef"
            placeholder="Fill setting key (unique)"
            v-model="innerModel.key"
            clearable
        >
            <template #prefix>
                <i class="el-icon-key"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Title:" prop="title" for="setting.title" required>
        <el-input
            size="large"
            placeholder="Fill setting title"
            v-model="innerModel.title"
            clearable
        >
            <template #prefix>
                <i class="el-icon-paragraph-left3"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Description:" prop="description" for="setting.description">
        <el-input
            size="large"
            type="textarea"
            placeholder="Setting description"
            v-model="innerModel.description"
        ></el-input>
    </el-form-item>
    <el-form-item label="Group:" v-if="innerModel.parent_id">
        <el-select
            v-model="innerModel.parent_id"
            class="el-width-1-1"
            size="large"
            placeholder="Select setting group"
            fit-input-width
            filterable 
        >
            <template #prefix>
                <i class="el-icon-bookmarks"></i>
            </template>
            <el-option
                v-for="group in tree"
                :key="group.id"
                :value="group.id"
                :label="group.title"
            >
                <div class="el-grid el-grid-sm el-flex-middle">
                    <div class="el-width-auto">
                        <i class="el-icon--left" :class="group.icon"></i>
                    </div>
                    <div class="el-width-expand">
                        <div class="el-select-dropdown__title" v-text="group.title"></div>
                        <div class="el-select-dropdown__subtitle" v-text="group.description"></div>
                    </div>
                </div>
            </el-option>
        </el-select>
    </el-form-item>
    <el-form-item label="Component:" v-if="innerModel.parent_id">
        <el-select
            v-model="innerModel.type"
            class="el-width-1-1"
            size="large"
            placeholder="Select interface component"
            fit-input-width
        >
            <template #prefix>
                <i class="el-icon-equalizer3"></i>
            </template>
            <el-option
                v-for="component in interfaceComponents"
                :key="component.name"
                :value="component.name"
                :label="component.title"
            >
                <i class="el-icon--left" :class="component.icon"></i>
                <span v-text="component.title"></span>
            </el-option>
        </el-select>
    </el-form-item>
    <el-form-item label="Icon:">
        <el-icon-picker
            placeholder="Select icon"
            size="large"
            class="el-width-1-1"
            v-model="innerModel.icon"
        ></el-icon-picker>
    </el-form-item>
    <div class="el-grid el-margin-md-top">
        <div class="el-width-expand">
            <el-button
                v-if="innerModel.id"
                size="large"
                type="danger"
                plain
                @click="onDelete"
            >
                <i class="el-icon-cancel-circle2 el-icon--left" />Delete
            </el-button>
        </div>
        <div class="">
            <el-button size="large" @click="onCancel">Cancel</el-button>
            <el-button
                size="large"
                native-type="submit"
                type="primary"
                :loading="localState.isLoading"
            >
                <template #icon>
                    <i class="el-icon-left el-icon-paperplane" />
                </template>
                Submit
            </el-button>
        </div>
    </div>
</el-form>
</template>

<script>
import { computed, nextTick, reactive, ref } from 'vue';
import { pick } from 'lodash';
import ElIconPicker from '@dashboard/components/icon-picker/index';
import {
    ElButton,
    ElInput,
    ElForm,
    ElFormItem,
    ElMessageBox,
    ElOption,
    ElSelect,
} from 'element-plus';
import { postSetting, deleteSetting } from '@dashboard/service/request/settings';

const interfaceComponents = [
    {
        name: 'input-text',
        title: 'Input text',
        icon: 'el-icon-pencil6',
    },
    {
        name: 'textarea',
        title: 'Textarea',
        icon: 'el-icon-typography',
    },
    {
        name: 'input-number',
        title: 'Input number',
        icon: 'el-icon-sort-numeric-asc',
    },
    {
        name: 'file',
        title: 'File upload',
        icon: 'el-icon-file-upload',
    },
];

export default {
    components: {
        ElButton,
        ElIconPicker,
        ElInput,
        ElForm,
        ElFormItem,
        ElMessageBox,
        ElOption,
        ElSelect,
    },

    props: {
        model: {
            type: Object,
            default: function(rawProps) {
                console.log('rawProps', rawProps);
                return {};
            },
        },
        tree: {
            type: Array,
        },
    },

    emits: {
        create: null,
        delete: null,
        update: null,
    },

    watch: {
        // 'innerModel.type': function(value) {
        //     nextTick(() => {
        //         this['type' + value + 'Ref'].focus();
        //     });
        // },
    },

    setup(props, { emit }) {
        const innerModel = ref(Object.assign({
            name: '',
            type: 'input-text',
        }, props.model));
        const formRef = ref(null);
        const settingKeyRef = ref();
        const localState = ref({
            isLoading: false,
        });
        const isRoot = computed(() => !props.model.parent_id);
        const validationRules = reactive({
            key: [
                { required: true, message: 'Please input key', trigger: 'change' },
                { min: 2, message: 'Length should be at least 2 symbol', trigger: 'change' },
            ],
            title: [
                { required: true, message: 'Please input title', trigger: 'change' },
                { min: 2, message: 'Length should be at least 2 symbol', trigger: 'change' },
            ],
        });

        const onSubmit = async (formEl) => {
            const validate = await formEl.validate((valid, fields) => valid);
            if (!validate) {
                return;
            }
            const payload = pick(innerModel.value, ['id', 'key', 'title', 'description', 'parent_id', 'type', 'icon']);
            const response = await postSetting(payload, {
                state: localState.value,
                notify: true,
            });
            if (response.error === 201) {
                emit('create', response.data);
                ElMessageBox.close();
            }
            if (response.error === 200) {
                emit('update', payload);
                ElMessageBox.close();
            }
        };

        const onDelete = function() {
            emit('delete');
        };

        const onCancel = function() {
            ElMessageBox.close();
        };

        nextTick(() => {
            settingKeyRef.value.focus();
        });

        return {
            innerModel,
            interfaceComponents,
            isRoot,
            formRef,
            settingKeyRef,
            onCancel,
            onDelete,
            onSubmit,
            localState,
            validationRules,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/input';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/select';
@use '~element-plus/theme-chalk/src/option';
</style>
