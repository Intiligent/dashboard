<template lang="html">
    <div class="el-grid el-flex-middle">
        <div class="">
            <h2 class="el-text--bold">Articles</h2>
        </div>
        <div class="el-width-expand">
            <span class="el-text--muted" v-text="'(' + collection.total + ')'"></span>
        </div>
        <div class="">
            <el-button @click="filterDrawer = true">
                <i class="el-icon-filter4 el-icon--left"></i>Filter
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
                <div class="" v-text="row.slug">

                </div>
                <div class="" v-text="row.text"></div>
            </template>
        </el-table-column>
        <el-table-column prop="active" width="40">
            <template #default="{row}">
                <span v-text="row.active"></span>
            </template>
        </el-table-column>
        <el-table-column prop="created_at" label="Created at" width="170" />
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

    <el-drawer
        title="Filter articles"
        size="360px"
        v-model="filterDrawer"
    >
        <el-form id="form-fliter" label-position="top">
            <!-- <el-form-item label="Name">
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
            <el-form-item label="Email">
                <el-input size="large" placeholder="Input user email" name="email" v-model="form.email">
                    <template #prefix>
                        <i class="el-icon-envelop2"></i>
                    </template>
                </el-input>
            </el-form-item> -->
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
import { inject, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
// import ModalMenuItem from '@dashboard/components/modal/modal.menu-item.vue';
import {
    ElButton,
    ElButtonGroup,
    ElDatePicker,
    ElDrawer,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElEmpty,
    ElForm,
    ElFormItem,
    ElInput,
    ElLink,
    ElPagination,
    ElTable,
    ElTableColumn,
} from 'element-plus';
import { getArticles } from '@dashboard/service/request/article';
//
export default {
    components: {
        ElButton,
        ElButtonGroup,
        ElDrawer,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElEmpty,
        ElForm,
        ElFormItem,
        ElInput,
        ElLink,
        ElPagination,
        ElTable,
        ElTableColumn,
    },

    inject: ['route', 'state'],

    watch: {
        'collection.current_page': function(value) {
            const payload = { page: value };
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
        const filterDrawer = ref(false);
        const router = useRouter();
        const form = reactive({
            title: null,
            slug: null,
            created_at: null,
        });

        return {
            collection,
            filterDrawer,
            form,
            route,
            router,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/drawer';
@use '~element-plus/theme-chalk/src/table';
@use '~element-plus/theme-chalk/src/link';
@use '~element-plus/theme-chalk/src/pagination';
@use '~element-plus/theme-chalk/src/empty';

@use '~element-plus/theme-chalk/src/form';
</style>
