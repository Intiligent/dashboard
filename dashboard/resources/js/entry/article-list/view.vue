<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="">
            <h2 class="el-text--bold">Articles</h2>
        </div>
        <div class="el-width-expand">
            <span class="el-text--muted" v-text="'(' + collection.total + ')'"></span>
        </div>
        <div class="">
            <el-badge class="el-margin-right" :value="activeFilters">
                <el-button @click="filterDrawer = true">
                    <i class="el-icon-filter4 el-icon--left"></i>Filter
                </el-button>
            </el-badge>
            <el-button
                type="primary"
                tag="a"
                :href="route('dashboard.article', {id: 'new'})"
            >
                <i class="el-icon-plus el-icon--left"></i>Add new article
            </el-button>
        </div>
    </div>

    <el-table :data="collection.data" style="width: 100%; padding-top: 20px;">
        <el-table-column prop="id" label="ID" width="50" />
        <el-table-column prop="title" label="Title">
            <template #default="{row}">
                <div class="">
                    <el-link
                        :href="route('dashboard.article', {id: row.id})"
                        :title="row.title"
                        type="primary"
                        v-text="row.title"
                        class="el-text--truncate"
                    ></el-link>
                </div>
                <div class="el-grid el-text--muted">
                    <div class="">
                        <i class="el-icon-calendar2 el-icon--left"></i>
                        <span title="Created at" v-text="row.created_at"></span>
                    </div>
                    <div class="">
                        <i class="el-icon-link el-icon--left"></i>
                        <span title="Slug" v-text="row.slug"></span>
                    </div>
                    <div class="">
                        <span title="Language" v-text="row.language.icon"></span>
                    </div>
                </div>
                <div class="" v-text="row.text"></div>
            </template>
        </el-table-column>
        <el-table-column prop="active" width="65">
            <template #default="{row}">
                <el-switch
                    v-model="row.active"
                    style="--el-switch-on-color: #13ce66;"
                    :active-value="1"
                    :inactive-value="0"
                    :loading="isItemActiveLoading[row.id]"
                    :before-change="() => onItemActiveChange(row)"
                ></el-switch>
            </template>
        </el-table-column>
        <template #empty>
            <el-empty></el-empty>
        </template>
    </el-table>

    <el-pagination
        class="el-flex-center el-pagination--large"
        style="padding-top: 30px;"
        background
        hide-on-single-page
        layout="prev, pager, next"
        :total="collection.total"
        :page-size="collection.per_page"
        :disabled="state.isLoading"
        v-model:current-page="collection.current_page"
    ></el-pagination>

    <pl-filter
        v-model="filterDrawer"
        :languages="languages"
        @submit="onFilter"
    ></pl-filter>
</template>

<script>
import { computed, inject, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import PlFilter from './components/filter.vue';
import {
    ElBadge,
    ElButton,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElEmpty,
    ElLink,
    ElPagination,
    ElSwitch,
    ElTable,
    ElTableColumn,
} from 'element-plus';
import {
    getArticles,
    postArticle,
} from '@dashboard/service/request/article';

export default {
    components: {
        ElBadge,
        ElButton,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElEmpty,
        ElLink,
        ElPagination,
        ElSwitch,
        ElTable,
        ElTableColumn,
        PlFilter,
    },

    inject: ['route'],

    watch: {
        'collection.current_page': function(value) {
            const payload = {
                filterModel: this.filterModel,
                page: value,
            };
            getArticles(payload, {
                state: this.state,
            }).then((response) => {
                this.collection = response.data;
                this.router.push({
                    path: location.pathname,
                    query: {page: value},
                });
            });
        },
    },

    setup() {
        const collection = ref(window.app.collection);
        const languages = ref(window.app.languages);
        const filterDrawer = ref(false);
        const filterModel = ref({});
        const isItemActiveLoading = ref({});
        const router = useRouter();
        const form = reactive({
            title: null,
            slug: null,
            created_at: null,
        });
        const state = inject('state');

        const activeFilters = computed(() => {
            return Object.values(filterModel.value).filter((value) => {
                if (Array.isArray(value) && !value.length) {
                    return false;
                }
                return value;
            }).length;
        });

        const onItemActiveChange = async function(model) {
            isItemActiveLoading.value[model.id] = true;
            try {
                const payload = { id: model.id, active: !model.active };
                const response = await postArticle(payload);
                if (response.error === 200) {
                    return true;
                }
            } finally {
                delete isItemActiveLoading.value[model.id];
            }
        };

        const onFilter = async function(model) {
            const payload = { filterModel: model };
            const response = await getArticles(payload, {
                state: state.value,
            });
            if (response.error === 200) {
                collection.value = response.data;
            }
            filterModel.value = model;
        };

        return {
            activeFilters,
            onItemActiveChange,
            collection,
            filterDrawer,
            filterModel,
            form,
            isItemActiveLoading,
            languages,
            onFilter,
            route,
            router,
            state,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/badge';
@use '~element-plus/theme-chalk/src/table';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/pagination';
@use '~element-plus/theme-chalk/src/empty';
@use '~element-plus/theme-chalk/src/switch';
</style>
