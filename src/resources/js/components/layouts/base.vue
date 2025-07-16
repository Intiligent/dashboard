<template lang="html">
    <main class="">
        <el-splitter>
            <el-splitter-panel :min="60" max="30%" size="300">
                <div class="section-sidebar-wrap">
                    <layout-sidebar></layout-sidebar>
                </div>
            </el-splitter-panel>
            <el-splitter-panel>
                <div class="section-content-wrap">
                    <layout-navbar>
                        <slot name="navbar"></slot>
                    </layout-navbar>
                    <div class="section-main">
                        <slot></slot>
                    </div>
                </div>
            </el-splitter-panel>
        </el-splitter>
    </main>
</template>

<script>
import LayoutSidebar from './sidebar';
import LayoutNavbar from './navbar';
import { ElSplitter, ElSplitterPanel } from 'element-plus';

export default {
    components: {
        ElSplitter,
        ElSplitterPanel,
        LayoutNavbar,
        LayoutSidebar,
    },

    setup() {
        return {
            //
        };
    },
};
</script>
