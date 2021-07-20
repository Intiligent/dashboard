<template>
<div class="el-grid el-grid-collapse el-height-1-1">
    <div class="el-width-auto sidebar">
        <table class="el-height-1-1 el-width-1-1">
            <tbody>
                <tr>
                    <td class="el-position-relative el-height-1-1">
                        <div class="el-overflow-container">
                            <el-radio-group vertical v-model="currentSource">
                                <el-radio-button :label="VIEW_GALLERY">
                                    <span>Галерея</span>
                                    <el-tag class="el-float-right" effect="dark" size="mini">1</el-tag>
                                </el-radio-button>
                                <el-radio-button :label="VIEW_LIBRARY">
                                    <span>Библиотека файлов</span>
                                    <el-tag class="el-float-right" effect="dark" size="mini">23</el-tag>
                                </el-radio-button>
                            </el-radio-group>

                            <div class="el-text-sm el-text-muted el-margin-top el-margin-sm-bottom">Загрузить файл(ы):</div>
                            <form class="el-margin-sm" method="post" @submit.prevent="handleSubmitUrl">
                                <el-input size="medium" prefix-icon="el-icon-link" placeholder="Загрузить по ссылке" v-model="urlValue">
                                    <el-button native-type="submit" size="medium" slot="append" icon="el-icon-forward"></el-button>
                                </el-input>
                            </form>

                            <!--
                            :on-preview="handlePreview"
                            :on-remove="handleRemove"
                            :file-list="fileList"
                             -->

                            <el-upload class="el-margin-sm" action="https://jsonplaceholder.typicode.com/posts/" drag multiple>
                                <i class="el-icon-upload"></i>
                                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
                                <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>
                            </el-upload>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="el-padding">
                        <div class="">bottom</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="el-width-expand">
        <table class="el-width-1-1 el-height-1-1">
            <tbody>
                <tr class="el-height-1-1">
                    <td style="padding: 20px 20px 0;">
                        <div class="el-position-relative el-height-1-1">
                            <div class="el-overflow-container">
                                <ul class="el-grid el-grid-collapse el-child-width-1-6 attachment-list" v-if="currentSource === VIEW_GALLERY">
                                    <li class="attachment">
                                        <div class="attachment-preview">
                                            <div class="thumbnail">
                                                <div class="centered">
                                                    <img src="https://miro.medium.com/max/1366/1*F9QwZsjxEcwZRD0CaKTBEQ.png" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                                <ul class="el-grid el-grid-collapse el-child-width-1-6 attachment-list" v-if="currentSource === VIEW_LIBRARY">
                                    <li class="attachment" v-for="media in libraryData" :key="media.id">
                                        <div class="attachment-preview" :title="`Имя файла: ${media.basename}\nРазмер файла: ${media.humansize}\nДата загрузки: ${media.created_at}` + (media.dimension ? `\nФормат изображения: ${media.dimension.width} x ${media.dimension.height} px` : '')">
                                            <div class="thumbnail">
                                                <div class="centered">
                                                    <img :src="media.icon || media.path" :alt="media.title" loading="lazy">
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="el-padding">
                        <div class="el-grid el-flex el-flex-middle">
                            <div class="el-width-auto">
                                Выделенные
                            </div>
                            <div class="el-width-auto">
                                Удалить выбранные
                            </div>
                            <div class="el-width-expand"></div>
                            <div class="el-width-auto">
                                <el-button type="primary" size="medium">Задать изображение</el-button>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</template>

<script>
import {
    Button,
    Input,
    RadioGroup,
    RadioButton,
    Slider,
    Upload,
    Tag,
} from 'element-ui';
import { getMediaLibrary } from '@dashboard/service/request/media.js';

const VIEW_GALLERY = 'gallery';
const VIEW_LIBRARY = 'library';

export default {
    name: 'MediaLibraryView',

    components: {
        ElButton: Button,
        ElRadioGroup: RadioGroup,
        ElRadioButton: RadioButton,
        ElSlider: Slider,
        ElUpload: Upload,
        ElInput: Input,
        ElTag: Tag,
    },

    data() {
        return {
            currentSource: VIEW_GALLERY,
            pageItems: 24,
            urlValue: null,
            library: {
                // элементы
                data: [],
                // текущая страница
                page: 1,
                // элементов на странице
                perPage: 36,
                // всего элементов
                total: 0,
            },
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

    created() {
        this.VIEW_GALLERY = VIEW_GALLERY;
        this.VIEW_LIBRARY = VIEW_LIBRARY;

        this.onInit();
    },

    methods: {
        onInit: function() {
            getMediaLibrary().then((response) => {
                console.log(response);

                this.library.data = response.data.data;
                this.library.page = response.data.current_page;
                this.library.perPage = response.data.per_page;
                this.library.total = response.data.total;
            });
        },

        handleSubmitUrl: function(event) {
            console.log(event);
        },
    },

    computed: {
        libraryData() {
            return this.library.data;
        },
    },
}
</script>

<style lang="scss">
@import "./../../../../style/theme/element";
@import "~element-ui/packages/theme-chalk/src/button.scss";
@import "~element-ui/packages/theme-chalk/src/message-box.scss";
@import "~element-ui/packages/theme-chalk/src/radio-group.scss";
@import "~element-ui/packages/theme-chalk/src/radio-button.scss";
@import "~element-ui/packages/theme-chalk/src/upload.scss";
@import "~element-ui/packages/theme-chalk/src/tag.scss";

$--media-attachment-gutter: 5px;

.sidebar {
    width: 280px;
    background: #f8f9fb;

    .el-overflow-container {
        padding: 20px 20px 0;
    }
}

.attachment-list {
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
    }
}
</style>
