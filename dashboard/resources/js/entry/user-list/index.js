import { createApp, provide } from 'vue';
import { createRouter, createWebHistory, useRouter } from 'vue-router';
import LayoutBase from '@dashboard/components/layout/base';
import LayoutUserList from './view';
import config from '@dashboard/plugin/config';

const router = createRouter({
    history: createWebHistory(),
    routes: [],
});

const app = createApp({
    components: {
        LayoutBase,
        LayoutUserList,
    },
});
app.use(config);
app.use(router);

router.isReady().then(() => {
    app.mount('#root');
});
