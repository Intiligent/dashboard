<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="">
            <h2 class="el-text--bold">Users</h2>
        </div>
        <div class="el-width-expand">
            <span class="el-text--muted" v-text="'(' + collection.total + ')'"></span>
        </div>
        <div class="">
            <el-button>
                <i class="el-icon-filter4 el-icon--left"></i>Filter
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

    <el-table :data="collection.data" style="width: 100%; padding-top: 20px;">
        <el-table-column prop="id" label="ID" width="50" />
        <el-table-column prop="avatar" width="36">
            <template #default="{row}">
                <el-avatar size="small" :src="row.avatar" style="vertical-align: bottom;" />
            </template>
        </el-table-column>
        <el-table-column prop="name" label="Name">
            <template #default="{row}">
                <el-link
                    :href="route('dashboard.user', {id: row.id})"
                    :title="row.name"
                    type="primary"
                    v-text="row.name"
                    class="el-text--truncate"
                ></el-link>
            </template>
        </el-table-column>
        <el-table-column prop="email" label="Email" />
        <el-table-column prop="created_at" label="Created at" width="170" />
        <template #empty>
            <el-empty></el-empty>
        </template>
    </el-table>

    <el-pagination
        class="el-flex-center"
        style="padding-top: 30px;"
        background
        hide-on-single-page
        layout="prev, pager, next"
        :total="collection.total"
        :page-size="collection.per_page"
        :disabled="state.isLoading"
        v-model:current-page="collection.current_page"
    ></el-pagination>
</template>

<script>
import { inject, ref } from 'vue';
import { useRouter } from 'vue-router';
// import ModalMenuItem from '@dashboard/components/modal/modal.menu-item.vue';
import {
    ElAvatar,
    ElButton,
    ElButtonGroup,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElEmpty,
    ElLink,
    ElPagination,
    ElTable,
    ElTableColumn,
} from 'element-plus';
import { getUsers } from '@dashboard/service/request/user';
//
export default {
    components: {
        ElAvatar,
        ElButton,
        ElButtonGroup,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElEmpty,
        ElLink,
        ElPagination,
        ElTable,
        ElTableColumn,
    },

    inject: ['route', 'state'],

    watch: {
        'collection.current_page': function(value) {
            const payload = { page: value };
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
        const collection = ref(window.app.collection.data);
        const router = useRouter();

        return {
            collection,
            route,
            router,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/avatar';
@use '~element-plus/theme-chalk/src/table';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/pagination';
@use '~element-plus/theme-chalk/src/empty';
</style>
