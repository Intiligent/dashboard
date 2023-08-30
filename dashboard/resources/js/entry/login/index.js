import { createApp, ref } from 'vue';
import {
    ElButton,
    ElContainer,
    ElInput,
    ElForm,
    ElFormItem,
} from 'element-plus';

createApp({
    components: {
        ElButton,
        ElContainer,
        ElInput,
        ElForm,
        ElFormItem,
    },

    setup() {
        const form = ref({
            login: null,
            password: null,
        });
        return {
            form,
        };
    },
}).mount('#root');
