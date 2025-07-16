'use strict';

import { createApp } from 'vue';
import LayoutView from './view';

const app = createApp({
    components: {
        LayoutView,
    },
});

app.mount('#root');
