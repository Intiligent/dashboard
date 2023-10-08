<template lang="html">
    <div class="">
        <el-empty description="404">
            <h2 class="el-text--bold">Page not fond</h2>
        </el-empty>
    </div>
</template>

<script>
import { ElEmpty } from 'element-plus';

export default {
    components: {
        ElEmpty,
    },
}
</script>

<style lang="scss">
@use '../../../style/theme/common';
@use '~element-plus/theme-chalk/src/empty';
</style>
