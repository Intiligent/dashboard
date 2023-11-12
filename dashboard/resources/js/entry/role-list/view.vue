<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="">
            <h2 class="el-text--bold">Roles</h2>
        </div>
        <div class="el-width-expand">
            <el-tag
                size="small"
                type="danger"
                effect="dark"
                v-text="collection.total"
                style="vertical-align: -5px;"
            ></el-tag>
        </div>
        <div class="">
            <el-badge class="" :value="activeFilters">
                <el-button @click="filterDrawer = true">
                    <i class="el-icon-filter4 el-icon--left"></i>Filter
                </el-button>
            </el-badge>
        </div>
        <div class="">
            <el-button
                type="primary"
                tag="a"
                :href="route('dashboard.role', {id: 'new'})"
            >
                <i class="el-icon-user-plus el-icon--left"></i>Add new role
            </el-button>
        </div>
    </div>

    <el-table
        :data="collection.data"
        @sort-change="onSortChanged"
        style="width: 100%; padding-top: 20px;"
    >
        <el-table-column prop="id" label="ID" width="64" sortable="custom" />
        <el-table-column prop="name" label="Name" sortable="custom">
            <template #default="{row}">
                <el-link
                    :href="route('dashboard.role', {id: row.id})"
                    :title="row.name"
                    type="primary"
                    v-text="row.name"
                    class="el-text--truncate"
                ></el-link>
            </template>
        </el-table-column>
        <el-table-column prop="guard_name" label="Guard name" sortable="custom" />
        <el-table-column prop="created_at" label="Created at" width="172" sortable="custom" />
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

    <pl-filter v-model="filterDrawer" @submit="onFilter"></pl-filter>
</template>

<script>
import { computed, inject, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import PlFilter from './components/filter.vue';
import {
    ElAvatar,
    ElBadge,
    ElButton,
    ElEmpty,
    ElLink,
    ElPagination,
    ElTable,
    ElTableColumn,
    ElTag,
} from 'element-plus';
import { getUsers } from '@dashboard/service/request/user';

export default {
    components: {
        ElAvatar,
        ElBadge,
        ElButton,
        ElEmpty,
        ElLink,
        ElPagination,
        ElTable,
        ElTableColumn,
        ElTag,
        PlFilter,
    },

    inject: ['route'],

    watch: {
        'collection.current_page': function(value) {
            const payload = { filterModel: this.filterModel, page: value };
            getUsers(payload, {
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
        const filterDrawer = ref(false);
        const filterModel = ref({});
        const router = useRouter();
        const state = inject('state');

        const activeFilters = computed(() => {
            return Object.values(filterModel.value).filter((value) => {
                if (Array.isArray(value) && !value.length) {
                    return false;
                }
                return value;
            }).length;
        });

        const onFilter = async function(model) {
            const payload = { filterModel: model };
            const response = await getUsers(payload, {
                state: state.value,
            });
            if (response.error === 200) {
                collection.value = response.data;
            }
            filterModel.value = model;
        };

        const onSortChanged = async function({ column, prop, order }) {
            const sortModel = [];
            const direction = (order) => order && order.replace(/ending$/, '');
            const sort = direction(order);
            sort && sortModel.push({ colId: prop, sort });
            const payload = { sortModel, filterModel: filterModel.value };
            const response = await getUsers(payload, {
                state: state.value,
            });
            if (response.error === 200) {
                collection.value = response.data;
            }
        };

        return {
            activeFilters,
            collection,
            filterDrawer,
            filterModel,
            onFilter,
            onSortChanged,
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
@use '~element-plus/theme-chalk/src/tag';
</style>
