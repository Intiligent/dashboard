<template lang="html">
    <section class="el-container el-height-1-1">
        <layout-navbar></layout-navbar>
        <section class="el-container is-vertical">
            <layout-header></layout-header>
            <main class="el-main" style="padding: 20px 30px 60px;">
                <slot></slot>
            </main>
        </section>
    </section>
</template>

<script>
import { computed, provide, reactive, readonly, ref } from 'vue';
import LayoutHeader from '@dashboard/components/layout/header';
import LayoutNavbar from '@dashboard/components/layout/navbar';

export default {
    components: {
        LayoutHeader,
        LayoutNavbar,
    },

    setup() {
        const collapseMenu = ref(null);
        const state = ref({
            isLoading: false,
        });
        const searchQuery = ref(null);
        const user = readonly(window.app.user);

        provide('collapseMenu', collapseMenu);
        provide('config', readonly(window.app.config));
        provide('state', state);
        provide('route', window.route);
        provide('searchQuery', searchQuery);
        provide('user', user);

        return {
            //
        };
    },
}
</script>

<style lang="scss">
</style>
