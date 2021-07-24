<template>
    <div class="test2">
        <el-button type="primary" plain @click="onMediaEditorClick">Задать изображение</el-button>
    </div>
</template>

<script>
import {
    Button,
    MessageBox,
} from 'element-ui';
import ElMediaLibraryView from './view.vue';

export default {
    name: 'MediaLibraryPreview',

    data() {
        return {
            state: {},
        };
    },

    props: {
        // model id
        entityId: {
            type: [Number, String],
            required: true,
        },
        // model type
        entityType: {
            type: [String],
            required: true,
        },
        entity: {
            type: String,
            default: 'default',
        },
    },

    components: {
        ElButton: Button,
        ElMessageBox: MessageBox,
    },

    created() {
        // this.onMediaEditorClick();
    },

    methods: {
        /**
         * Нажатие на кнопку медиа редактора
         *
         * @param {Object} event [MouseClick]
         * @return {void}
         */
        onMediaEditorClick: function(event = null) {
            const h = this.$createElement;

            // console.log(JSON.stringify(this.$props));

            return MessageBox({
                message: h(ElMediaLibraryView, {
                    props: this.$props,
                    on: {
                        // input: (value) => {
                        //     this.onMenuUpdate(model, value, node)
                        // },
                        // created: (value) => {
                        //     this.appendToNode(value, parent);
                        // },
                        // destroy: (model) => {
                        //     this.onDestroy(model, node).then((count) => {
                        //         if (count) {
                        //             MessageBox.close();
                        //         }
                        //     });
                        // },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                size: 'large',
                customClass: 'el-message-box--collapse el-message-box--viewport',
            }).catch((state) => {
                console.log('action:', state);
            });
        },
    },
}
</script>

<style lang="css">
.test {
    color: red;
}
</style>
