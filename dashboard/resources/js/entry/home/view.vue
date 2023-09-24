<template lang="html">
    <div class="">
        Home dashboard
    </div>
</template>

<script>
import { ref } from 'vue';
import {
    ElButton,
} from 'element-plus';

export default {
    components: {
        ElButton,
    },

    setup() {
        return {
            //
        };
    },
}
</script>

<style lang="scss">
@use '~element-plus/theme-chalk/src/mixins/function' as *;
@use '../../../style/theme/common';

// @use '~element-plus/theme-chalk/src/tabs';
</style>
