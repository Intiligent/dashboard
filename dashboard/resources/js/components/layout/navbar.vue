<template lang="html">
    <aside class="el-aside">
        <div class="" style="height: 70px; border-bottom: 1px solid var(--el-border-color-light);">
            <a :href="route('dashboard.home')" class="el-height-1-1 el-menu-item" style="display: block;">
                <span class="el-grid el-grid-sm el-flex el-flex-middle el-height-inherit">
                    <span class="el-width-auto">
                        <span class="el-avatar el-avatar--circle" style="padding: 5px;">
                            <img :src="config.LOGO_SQUARE || '/dashboard/img/logo-square.png'" style="object-fit: cover;">
                        </span>
                    </span>
                    <span class="el-width-expand">
                        <div class="el-text--truncate pl-logo--title">{{ config.PROJECT_NAME || 'App name' }}</div>
                        <div class="el-text--truncate pl-logo--description">{{ config.META_TITLE }}</div>
                    </span>
                </span>
            </a>
        </div>

        <el-menu :collapse="collapseMenu" router style="padding-top: 20px;">
            <!-- <el-menu-item index="22-1">Dashboard</el-menu-item> -->
            <el-menu-item-group>
                <template #title>Content</template>
                <el-menu-item index="22-1">
                    <i class="el-icon-bookmark"></i>
                    <template #title>Article pages</template>
                </el-menu-item>
                <el-menu-item index="22-2">
                    <i class="el-icon-images2"></i>
                    <template #title>Banners</template>
                </el-menu-item>
            </el-menu-item-group>
            <el-menu-item-group>
                <template #title>System</template>
                <el-menu-item index="22-1">
                    <i class="el-icon-list-unordered"></i>
                    <template #title>Menu</template>
                </el-menu-item>
                <el-menu-item index="22-2">
                    <i class="el-icon-users2"></i>
                    <template #title>Users</template>
                </el-menu-item>
                <el-sub-menu index="2">
                    <template #title>
                        Access Control
                    </template>
                    <el-menu-item-group>
                        <el-menu-item index="2-1">Option 1</el-menu-item>
                        <el-menu-item index="2-2">Option 2</el-menu-item>
                    </el-menu-item-group>
                </el-sub-menu>
                <el-menu-item route="setting" index="22-2">
                    <i class="el-icon-cog"></i>
                    <template #title>Settings</template>
                </el-menu-item>
            </el-menu-item-group>
            <el-menu-item-group>
                <template #title>Website</template>
                <el-menu-item index="22-1">
                    <i class="el-icon-enter5"></i>
                    <template #title>Go to website</template>
                </el-menu-item>
            </el-menu-item-group>
        </el-menu>
    </aside>
</template>

<script>
import {
    ElMenu,
    ElMenuItem,
    ElMenuItemGroup,
    ElSubMenu,
} from 'element-plus';

export default {
    components: {
        ElMenu,
        ElMenuItem,
        ElMenuItemGroup,
        ElSubMenu,
    },

    inject: ['config', 'collapseMenu', 'route'],

    setup() {
        return {
            //
        };
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/menu';

// ElAside
aside.el-aside {
    background-color: #f4f6f9;
    --el-aside-width: 280px;
}
.pl-logo--title {
    color: var(--el-text-color-primary);
    font-weight: bold;
    font-size: 16px;
    line-height: 24px;
}
.pl-logo--description {
    color: var(--el-text-color-secondary);
    font-size: 14px;
    line-height: 18px;
}
</style>
