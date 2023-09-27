<template lang="html">
    <section
        class="el-container el-height-1-1"
        :class="{'el-container--expand': config.LAYOUT_BOX_SIZE === 'full'}"
    >
        <layout-navbar></layout-navbar>
        <section class="el-width-1-1 is-vertical">
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
        const config = readonly(window.app.config);
        const state = ref({
            isLoading: false,
        });
        const searchQuery = ref(null);
        const user = readonly(window.app.user);

        provide('collapseMenu', collapseMenu);
        provide('config', config);
        provide('state', state);
        provide('route', window.route);
        provide('searchQuery', searchQuery);
        provide('user', user);

        return {
            config,
        };
    },
}
</script>

<style lang="scss">
</style>
