import { createApp, ref } from 'vue';
import {
    ElButton,
    ElCheckbox,
    ElContainer,
    ElInput,
    ElForm,
    ElFormItem,
} from 'element-plus';

createApp({
    components: {
        ElButton,
        ElCheckbox,
        ElContainer,
        ElInput,
        ElForm,
        ElFormItem,
    },

    setup() {
        const form = ref({
            email: null,
            password: null,
            remember: false,
        });
        return {
            form,
        };
    },
}).mount('#root');
