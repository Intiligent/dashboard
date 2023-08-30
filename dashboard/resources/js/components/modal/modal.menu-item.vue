<template lang="html">
<el-form
    label-position="left"
    require-asterisk-position="right"
    label-width="150px"
    @submit.prevent="onSubmit"
>
    <el-form-item label="ID:">
        <span v-text="innerModel.id"></span>
    </el-form-item>
    <el-form-item label="Title:" for="menu.title" required>
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
    <el-form-item label="Link type:">
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
                {{ type.title }}
            </el-radio-button>
        </el-radio-group>
    </el-form-item>
    <el-form-item>
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
    <el-form-item label="Attributes:" for="menu.attribute">
        <el-input
            size="large"
            id="menu.attribute"
            placeholder='data-type="menu"'
            v-model="innerModel.attribute"
            clearable
        >
            <template #prefix>
                <i class="el-icon-code"></i>
            </template>
        </el-input>
    </el-form-item>
    <el-form-item label="Parent:" for="menu.parent">
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
                size="large"
                type="danger"
                plain
            ><el-icon class="el-icon--left"><i class="el-icon-cancel-circle2" /></el-icon> Delete</el-button>
        </div>
        <div class="">
            <el-button size="large">Cancel</el-button>
            <el-button
                size="large"
                native-type="submit"
                type="primary"
            >Submit<el-icon class="el-icon--right"><i class="el-icon-paperplane" /></el-icon></el-button>
        </div>
    </div>
</el-form>
</template>

<script>
import { nextTick, ref } from 'vue';
import ElIconPicker from '@dashboard/components/icon-picker/index';
import {
    ElButton,
    ElCascader,
    ElIcon,
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
import { postMenu } from '@dashboard/service/request/menu';

const TYPE_URI = 'uri';
const TYPE_ROUTE = 'route';
const TYPE_ARTICLE = 'article';

export default {
    components: {
        ElButton,
        ElCascader,
        ElIcon,
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
    },

    watch: {
        'innerModel.type': function(value) {
            nextTick(() => {
                this['type' + value + 'Ref'].focus();
            });
        },
    },

    setup(props, { emit }) {
        const innerModel = ref({...props.model});
        const cascaderMenuProps = {
            checkStrictly: true,
            emitPath: false,
            value: 'id',
            label: 'name',
        };
        const menuTitleRef = ref();
        const typeuriRef = ref(null);
        const typerouteRef = ref(null);
        const typearticleRef = ref(null);

        const onSubmit = async () => {
            console.log(innerModel.value);
            const payload = innerModel.value;
            const response = await postMenu(payload);
            console.log('response', response);
            if (response.error === 201) {
                emit('create', response.data);
                ElMessageBox.close();
            }
        };

        nextTick(() => {
            menuTitleRef.value.focus();
        });

        return {
            cascaderMenuProps,
            innerModel,
            menuTitleRef,
            MENU_TYPE_LIST: window.app.MENU_TYPE_LIST,
            onSubmit,
            typeuriRef,
            typerouteRef,
            typearticleRef,
            TYPE_URI,
            TYPE_ROUTE,
            TYPE_ARTICLE,
        };
    },
}
</script>

<style lang="scss">
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
