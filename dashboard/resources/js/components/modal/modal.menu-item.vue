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
    <el-form-item label="Title:" prop="name" for="menu.title" required>
        <el-input
            size="large"
            ref="menuTitleRef"
            id="menu.title"
            placeholder="Fill menu title"
            v-model="innerModel.name"
            clearable
        >
            <template #prefix>
                <i class="el-icon-paragraph-left3"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Link type:" v-if="!isRoot">
        <el-radio-group
            v-model="innerModel.type"
            size="large"
            class="is-stretch el-width-1-1"
        >
            <el-radio-button
                v-for="type in MENU_TYPE_LIST"
                :key="type.name"
                :label="type.name"
            >
                <i class="el-icon--left el-icon-08x" :class="type.icon"></i>{{ type.title }}
            </el-radio-button>
        </el-radio-group>
    </el-form-item>
    <el-form-item prop="value" v-if="!isRoot">
        <template #label>
            <el-link type="primary">follow the link</el-link>
        </template>
        <el-input
            v-if="innerModel.type === TYPE_URI"
            ref="typeuriRef"
            size="large"
            placeholder="Input link"
            v-model="innerModel.value"
            clearable
        >
            <template #prefix>
                <i class="el-icon-link2"></i>
            </template>
        </el-input>
        <el-select
            v-if="innerModel.type === TYPE_ROUTE"
            ref="typerouteRef"
            v-model="innerModel.value"
            class="el-width-1-1"
            size="large"
            placeholder="Select route for link"
            clearable
            filterable
        >
            <template #prefix>
                <i class="el-icon-bookmark"></i>
            </template>
            <el-option
                v-for="route in routes"
                :key="route.name"
                :value="route.name"
                :label="route.uri"
            >
                <span v-text="route.name" style="float: left;"></span>
                <span v-text="route.uri" style="float: right; color: var(--el-text-color-placeholder); font-size: 13px;"></span>
            </el-option>
        </el-select>
        <el-input
            v-if="innerModel.type === TYPE_ARTICLE"
            ref="typearticleRef"
            placeholder="Select article page"
            size="large"
            v-model="innerModel.value"
        >
            <template #prefix>
                <i class="el-icon-magazine"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Icon:">
        <el-icon-picker
            placeholder="Select icon"
            size="large"
            class="el-width-1-1"
            v-model="innerModel.icon"
        ></el-icon-picker>
    </el-form-item>
    <el-form-item label="Attributes:" for="menu.attribute" v-if="!isRoot">
        <el-input
            size="large"
            id="menu.attribute"
            placeholder='html attributes for link'
            v-model="innerModel.attribute"
            clearable
        >
            <template #prefix>
                <i class="el-icon-code"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Parent:" for="menu.parent" v-if="!isRoot">
        <el-cascader
            size="large"
            id="menu.parent"
            class="el-width-1-1"
            placeholder="Select parent menu"
            :show-all-levels="false"
            :options="tree"
            :props="cascaderMenuProps"
            v-model="innerModel.parent_id"
            filterable
            clearable
        ></el-cascader>
    </el-form-item>
    <el-form-item label="Active:" for="menu.active">
        <el-switch
            id="menu.active"
            v-model="innerModel.active"
            style="--el-switch-on-color: #13ce66;"
        ></el-switch>
    </el-form-item>
    <div class="el-grid">
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
import ElIconPicker from '@dashboard/components/icon-picker/index';
import {
    ElButton,
    ElCascader,
    ElInput,
    ElForm,
    ElFormItem,
    ElLink,
    ElMessageBox,
    ElOption,
    ElRadioGroup,
    ElRadioButton,
    ElSelect,
    ElSwitch,
} from 'element-plus';
import { postMenu, deleteMenu } from '@dashboard/service/request/menu';

const TYPE_URI = 'uri';
const TYPE_ROUTE = 'route';
const TYPE_ARTICLE = 'article';

export default {
    components: {
        ElButton,
        ElCascader,
        ElIconPicker,
        ElInput,
        ElForm,
        ElFormItem,
        ElLink,
        ElMessageBox,
        ElOption,
        ElRadioGroup,
        ElRadioButton,
        ElSelect,
        ElSwitch,
    },

    props: {
        model: {
            type: Object,
        },
        routes: {
            type: Array,
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
        'innerModel.type': function(value) {
            nextTick(() => {
                this['type' + value + 'Ref'].focus();
            });
        },
    },

    setup(props, { emit }) {
        const cascaderMenuProps = {
            checkStrictly: true,
            emitPath: false,
            value: 'id',
            label: 'name',
        };
        const innerModel = ref(Object.assign({
            name: '',
            value: '',
            type: 'uri',
        }, props.model));
        const formRef = ref(null);
        const menuTitleRef = ref();
        const typeuriRef = ref(null);
        const typerouteRef = ref(null);
        const typearticleRef = ref(null);
        const localState = ref({
            isLoading: false,
        });
        const isRoot = computed(() => !props.model.parent_id);
        const validationRules = reactive({
            name: [
                { required: true, message: 'Please input title', trigger: 'change' },
            ],
            value: [
                {
                    validator: (rule, value, callback) => {
                        if (!isRoot.value && !value) {
                            callback(new Error('Please input link value'));
                        }
                        callback();
                    },
                    trigger: 'change',
                },
            ],
        });

        const onSubmit = async (formEl) => {
            const validate = await formEl.validate((valid, fields) => valid);
            if (!validate) {
                return;
            }
            const payload = innerModel.value;
            const response = await postMenu(payload, {
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
            menuTitleRef.value.focus();
        });

        return {
            cascaderMenuProps,
            innerModel,
            isRoot,
            formRef,
            menuTitleRef,
            MENU_TYPE_LIST: window.app.MENU_TYPE_LIST,
            onCancel,
            onDelete,
            onSubmit,
            localState,
            typeuriRef,
            typerouteRef,
            typearticleRef,
            TYPE_URI,
            TYPE_ROUTE,
            TYPE_ARTICLE,
            validationRules,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/input';
@use '~element-plus/theme-chalk/src/form';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/cascader';
@use '~element-plus/theme-chalk/src/cascader-panel';
@use '~element-plus/theme-chalk/src/scrollbar';
@use '~element-plus/theme-chalk/src/radio-group';
@use '~element-plus/theme-chalk/src/radio-button';
@use '~element-plus/theme-chalk/src/radio';
@use '~element-plus/theme-chalk/src/select';
@use '~element-plus/theme-chalk/src/option';
</style>
