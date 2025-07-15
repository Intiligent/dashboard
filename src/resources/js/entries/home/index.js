import { createApp } from 'vue';
import { get } from 'lodash';

const app = createApp({
    setup() {
        get('r', {r: 123});
        console.log('setup home dashboard');
    },
});

app.mount('#root');
