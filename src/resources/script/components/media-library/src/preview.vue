<template>
    <div class="">
        <ul class="el-grid el-grid-collapse el-child-width-1-6 el-margin-sm" v-if="galleryCollection.length">
            <li class="attachment" v-for="model in galleryCollection">
                <div class="attachment-preview">
                    <div class="thumbnail">
                        <div class="centered">
                            <img :src="model.path" :alt="model.title">
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="el-grid el-grid-xs el-flex el-flex-middle">
            <div class="el-width-auto">
                <el-button type="primary" :icon="multiple ? 'el-icon-images2' : 'el-icon-attachment'" plain @click="onMediaEditorClick">Задать изображение</el-button>
            </div>
            <div class="el-width-auto">
                всего: <span class="el-text-bold" v-text="source.length"></span>
            </div>
            <div class="el-width-expand el-text-truncate">
                <a target="_blank" href="model.path" title="fileName"></a>
            </div>
        </div>
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

    components: {
        ElButton: Button,
        ElMessageBox: MessageBox,
    },

    data() {
        return {
            state: {
                view: false,
            },
        };
    },

    props: {
        // model id
        modelId: {
            type: [Number, String],
            required: true,
        },
        // model name
        modelType: {
            type: [String],
            required: true,
        },
        entity: {
            type: String,
            default: 'default',
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        // data source
        source: {
            type: [Object, Array],
        },
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
            this.state.view = true;

            // console.log(JSON.stringify(this.$props));

            return MessageBox({
                message: h(ElMediaLibraryView, {
                    props: {...this.$props, state: this.state},
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
                this.state.view = false;
            });
        },
    },

    computed: {
        // fileName() {
        //     return _.get(this.model, 'path', '').replace(/^.*[\\\/]/, '');
        // },

        galleryCollection() {
            if (!this.source) {
                return [];
            }
            if (_.isArray(this.source)) {
                return this.source.slice(0, 12);
            }
            return [this.source];
        },
    },
}
</script>

<style lang="scss">
@import "./../../../../style/theme/element";

$--media-attachment-gutter: 5px;

.attachment {
    position: relative;
    padding: $--media-attachment-gutter;
    margin: 0;
    text-align: center;
    box-sizing: border-box;
    .attachment-preview {
        position: relative;
        box-shadow: inset 0 0 15px rgba(0,0,0,.1), inset 0 0 0 1px rgba(0,0,0,.05);
        background: #eee;
        cursor: pointer;
        &::before {
            content: '';
            display: block;
            padding-top: 100%;
        }
        .thumbnail {
            overflow: hidden;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            opacity: 1;
            transition: opacity .1s;
            &::after {
                content: '';
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                box-shadow: inset 0 0 0 1px rgba(0,0,0,.1);
                overflow: hidden;
            }
            .centered {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                transform: translate(50%, 50%);
                img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    max-height: 100%;
                    max-width: none;
                    transform: translate(-50%, -50%);
                }
            }
        }
    }
    &.selected {
        box-shadow: inset 0 0 0 2px $--fill-base, inset 0 0 0 5px $--background-color-primary;
    }
}
</style>
