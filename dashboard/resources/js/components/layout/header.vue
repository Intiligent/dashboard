<template lang="html">
    <header class="el-header" style="padding-left: 30px; padding-right: 30px;">
        <nav class="el-grid el-flex el-flex-middle" style="height: 70px;">
            <div class="">
                <el-button
                    size="large"
                    type="primary"
                    class="is-narrow"
                    plain
                    @click="collapseMenu = !collapseMenu"
                >
                    <i :class="collapseMenu ? 'el-icon-cross2' : 'el-icon-menu7'"></i>
                </el-button>
            </div>
            <div class="el-width-expand">
                <form method="get">
                    <el-input
                        size="large"
                        v-model="searchQuery"
                        placeholder="Type some text to search"
                        name="q"
                        clearable
                    >
                        <template #prefix>
                            <i class="el-icon-search4"></i>
                        </template>
                        <template #append>
                            <el-button native-type="submit">Search</el-button>
                        </template>
                    </el-input>
                </form>
            </div>
            <div class="" style="min-width: 18px;">
                <span class="spinner-icon" v-show="state.isLoading"></span>
            </div>
            <div class="">
                <!-- @command="handleCommand" -->
                <el-dropdown
                    trigger="click"
                    size="large"
                    placement="bottom-end"
                >
                    <div class="el-dropdown-link">
                        <div class="el-grid el-grid-xs el-flex-middle">
                            <div class="el-text--right">
                                <div class="el-text--bold" style="line-height: 20px;" v-text="user.name"></div>
                                <div class="el-text--small el-text--placeholder" style="line-height: 18px;" v-text="user.email"></div>
                            </div>
                            <div class="">
                                <i class="el-icon-arrow-down22"></i>
                            </div>
                            <div class="">
                                <el-avatar :src="user.avatar"/>
                            </div>
                        </div>
                    </div>
                    <template #dropdown>
                        <el-dropdown-menu>
                            <el-dropdown-item class="el-dropdown--link">
                                <a :href="route('dashboard.user', {id: user.id})">
                                    <i class="el-icon-user"></i>My profile
                                </a>
                            </el-dropdown-item>
                            <el-dropdown-item command="d" disabled>Action with long name</el-dropdown-item>
                            <el-dropdown-item class="el-dropdown--link" divided>
                                <a :href="route('dashboard.logout')">
                                    <i class="el-icon-exit"></i>Logout
                                </a>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </nav>
    </header>
</template>

<script>
import { inject, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import store from 'store';
import {
    ElAvatar,
    ElButton,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElInput,
} from 'element-plus';

export default {
    components: {
        ElAvatar,
        ElButton,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElInput,
    },

    inject: ['route', 'state', 'user'],

    setup() {
        const collapseMenu = inject('collapseMenu');
        const currentRoute = useRoute();
        const searchQuery = inject('searchQuery');
        if (currentRoute.query.q) {
            searchQuery.value = currentRoute.query.q;
        }
        watch(collapseMenu, (value) => {
            store.set('collapseMenu', value);
        });

        return {
            collapseMenu,
            searchQuery,
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/avatar';
@use '~element-plus/theme-chalk/src/dropdown';
@use '~element-plus/theme-chalk/src/popper';
@use '~element-plus/theme-chalk/src/input';

form {
    .el-input .el-input-group__append {
        padding-left: 20px;
        padding-right: 20px;
    }
}
</style>
