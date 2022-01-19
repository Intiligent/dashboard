<template>
<div class="el-grid el-grid-collapse el-height-1-1">
    <div class="el-width-auto sidebar">
        <table class="el-height-1-1 el-width-1-1">
            <tbody>
                <tr>
                    <td class="el-position-relative el-height-1-1">
                        <div class="el-overflow-container" v-if="currentFile">
                            <div class="el-text-bold el-margin-sm">Параметры файла</div>
                            <div class="el-margin-sm attachment-info">
                                <div class="thumbnail">
                                    <a :href="currentFile.path">
                                        <img :src="currentFile.path" :alt="currentFile.title">
                                    </a>
                                </div>
                            </div>
                            <div class="el-margin" style="font-size: 14px;">
                                <div class="el-margin-sm">
                                    <label class="el-text-muted"><i class="el-icon-calendar2"></i> Дата загрузки:</label>
                                    <div class="el-margin-xs" v-text="currentFile.created_at"></div>
                                </div>
                                <div class="el-margin-sm">
                                    <label class="el-text-muted"><i class="el-icon-calendar2"></i> Дата обновления:</label>
                                    <div class="el-margin-xs" v-text="currentFile.updated_at"></div>
                                </div>
                                <div class="el-margin-sm" v-if="currentFile.humansize">
                                    <label class="el-text-muted"><i class="el-icon-law"></i> Размер файла:</label>
                                    <div class="el-margin-xs" v-text="currentFile.humansize"></div>
                                </div>
                                <div class="el-margin-sm">
                                    <label class="el-text-muted"><i class="el-icon-rulers"></i> Формат изображения:</label>
                                    <div class="el-margin-xs">1160 x 300 px</div>
                                </div>
                                <div class="el-margin-sm el-text-break">
                                    <label class="el-text-muted"><i class="el-icon-link2"></i> Путь к файлу:</label>
                                    <div class="el-margin-xs" v-text="currentFile.path"></div>
                                    <a class="el-text-primary" target="_blank" :href="currentFile.path"><i class="el-icon-new-tab2"></i></a>
                                </div>
                            </div>
                            <div class="el-grid el-grid-xs el-flex el-flex-middle el-margin-xs">
                                <div class="el-width-expand">
                                    <el-button class="el-width-1-1" size="medium" type="primary" icon="el-icon-pencil">Редактировать</el-button>
                                </div>
                                <div class="el-width-auto">
                                    <el-button size="medium" type="danger" icon="el-icon-bin" narrow @click="onDestroyMedia(currentFile)"></el-button>
                                </div>
                            </div>
                        </div>
                        <div class="el-overflow-container" v-else>
                            <el-radio-group vertical v-model="currentView">
                                <el-radio-button :label="VIEW_GALLERY">
                                    <span>Галерея</span>
                                    <el-tag class="el-float-right" effect="dark" size="mini" v-text="galleryCollection.length"></el-tag>
                                </el-radio-button>
                                <el-radio-button :label="VIEW_LIBRARY">
                                    <span>Библиотека файлов</span>
                                    <el-tag class="el-float-right" effect="dark" size="mini">23</el-tag>
                                </el-radio-button>
                            </el-radio-group>

                            <div class="el-margin-top el-margin-sm-bottom">Загрузить файл(ы):</div>
                            <form class="el-margin-sm" method="post" @submit.prevent="handleSubmitUrl">
                                <el-input size="medium" prefix-icon="el-icon-link" placeholder="Загрузить по ссылке" v-model="urlValue">
                                    <el-button native-type="submit" size="medium" slot="append" icon="el-icon-forward" style="min-width: 10px;"></el-button>
                                </el-input>
                            </form>

                            <!--
                            :on-preview="handlePreview"
                            :on-remove="handleRemove"
                            :file-list="fileList"
                             -->

                            <el-upload
                                class="el-margin-sm"
                                name="files"
                                action="/api/dashboard/media/files"
                                drag
                                :data="uploadPayload"
                                :limit="10"
                                :multiple="multiple"
                                :headers="headers"
                                :on-success="onUploadSuccess"
                            >
                                <i class="el-icon-upload"></i>
                                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
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
                                <template v-if="currentView === VIEW_GALLERY">
                                    <ul class="el-grid el-grid-collapse el-child-width-1-6 attachment-list" v-if="galleryCollection.length">
                                        <li class="attachment" :class="{'selected': selectedIncrement && selected.has(model.id)}" v-for="model in galleryCollection" @click="onItemClick(model, $event)">
                                            <div class="attachment-preview">
                                                <div class="thumbnail">
                                                    <div class="centered">
                                                        <img :src="model.path" :alt="model.title">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <el-empty description="Gallery data no found" style="min-height: 100%;" v-else></el-empty>
                                </template>

                                <ul class="el-grid el-grid-collapse el-child-width-1-6 attachment-list" v-if="currentView === VIEW_LIBRARY">
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
                        <div class="el-grid el-grid-sm el-flex el-flex-middle">
                            <div class="el-width-auto" v-if="selectedIncrement && selected.size">
                                Выделенные: <span class="el-text-bold" v-text="selected.size"></span>
                            </div>
                            <div class="el-width-auto" v-if="selectedIncrement && selected.size">
                                <el-link type="danger" icon="el-icon-stack-cancel" @click.prevent="onDestroySelected">Удалить выбранные</el-link>
                            </div>
                            <div class="el-width-expand"></div>
                            <div class="el-width-auto" v-if="currentView === VIEW_LIBRARY">
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
    Empty,
    Input,
    Link,
    RadioGroup,
    RadioButton,
    Slider,
    Upload,
    Tag,
} from 'element-ui';
import {
    getMediaLibrary,
    postMediaUploadFromUrl,
    deleteMedia,
} from '@dashboard/service/request/media.js';

const VIEW_GALLERY = 'gallery';
const VIEW_LIBRARY = 'library';

export default {
    name: 'MediaLibraryView',

    components: {
        ElButton: Button,
        ElEmpty: Empty,
        ElInput: Input,
        ElLink: Link,
        ElRadioGroup: RadioGroup,
        ElRadioButton: RadioButton,
        ElSlider: Slider,
        ElUpload: Upload,
        ElTag: Tag,
    },

    data() {
        return {
            currentFile: null,
            currentView: VIEW_GALLERY,
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
            // выделенные модели
            selected: new Map(),
            // трекер изменений для selected Map
            selectedIncrement: 0,
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
            default: function() {
                return [];
            },
        },
        state: {
            type: [Object],
        },
    },

    created() {
        this.VIEW_GALLERY = VIEW_GALLERY;
        this.VIEW_LIBRARY = VIEW_LIBRARY;

        this.onInit();
    },

    beforeDestroy() {
        console.log('beforeDestroy');
    },

    destroyed() {
        console.log('destroyed');
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

        onClose: function() {
            this.setCurrentFile(null);
            this.currentView = VIEW_GALLERY;
            this.urlValue = null;
            this.clearSelected();
        },

        /**
         * Click model item
         *
         * @param {Object} model [media model]
         * @param {Object} event [mouseClick]
         * @return {void}
         */
        onItemClick: function(model, event) {
            // множественное выделение
            if (!(event.metaKey || event.ctrlKey)) {
                this.clearSelected();
            }
            const currentId = _.get(this.currentFile, 'id');
            if (this.isSelected(model) || currentId === model.id) {
                // снять выделение выбранного файла
                this.setCurrentFile(null);
                // снять выделение
                this.deleteSelected(model);
            } else {
                // установить текущий файл
                this.setCurrentFile(model);
                // добавить выделение
                this.addSelected(model);
            }
        },

        /**
         * hook function when uploaded successfully
         *
         * @param {[type]} response               [description]
         * @param {[type]} file                   [description]
         * @param {[type]} fileList               [description]
         * @return {[type]}          [description]
         */
        onUploadSuccess: function(response, file, fileList) {
            console.log(response);
            console.log(file);
            console.log(fileList);

            const files = _.get(response.data, 'items', []);
            files.forEach((model) => {
                this.source.push(model);
            });
        },

        /**
         * Удалить текущий выбранный файл
         *
         * @param {Object} model [media model]
         * @return {void}
         */
        onDestroyMedia: function(model) {
            if (!confirm(`Вы действительно хотите удалить обьект ${model.id}?`)) {
                return;
            }
            const payload = {id: model.id};
            return deleteMedia(payload, {notify: true}).then((response) => {
                // удалить модель из коллекции
                this.destroyGalleryModel(model);
                // удалить из выделенных
                this.deleteSelected(model);
                // снять выделение выбранного файла
                this.setCurrentFile(null);
            });
        },

        /**
         * Удалить выбранные элементы
         *
         * @return {[type]} [description]
         */
        onDestroySelected: function() {
            if (this.selected.size && !confirm(`Вы действительно хотите удалить ${this.selected.size} обьект(а)?`)) {
                return;
            }
            const payload = {id: Array.from(this.selected.keys())};
            return deleteMedia(payload, {notify: true}).then((response) => {
                Array.from(this.selected.values()).forEach((model) => {
                    // удалить модель из коллекции
                    this.destroyGalleryModel(model);
                });
                // очистить все выбранные файлы
                this.clearSelected();
                // снять выделение выбранного файла
                this.setCurrentFile(null);
            });
        },

        /**
         * Отправка формы загрузки по ссылке
         *
         * @return {Promise}
         */
        handleSubmitUrl: function() {
            const payload = {
                model_id: this.modelId,
                model_type: this.modelType,
                entity: this.entity,
                multiple: this.multiple,
                value: this.urlValue,
            };
            return postMediaUploadFromUrl(payload, {notify: true}).then((response) => {
                this.source.push(response.data);
                this.urlValue = null;
            });
        },

        /**
         * Установить выделенный файл
         *
         * @param {Object|null} [model=null] [media model]
         */
        setCurrentFile(model = null) {
            this.currentFile = model;
        },

        /**
         * Is model to selected
         * true if an element with the specified key exists in the Map object; otherwise false.
         *
         * @param {Object} model [media model]
         * @return {Boolean}
         */
        isSelected: function(model) {
            return this.selected.has(model.id);
        },

        /**
         * Add model to selected
         *
         * @param {Object} model [media model]
         * @return {Map}
         */
        addSelected: function(model) {
            const value = this.selected.set(model.id, model);
            ++this.selectedIncrement;
            return value;
        },

        /**
         * Delete model from selected
         *
         * @param {Object} model [media model]
         * @return {Boolean} [true if an element has been removed, or false if the element does not exist]
         */
        deleteSelected: function(model) {
            const action = this.selected.delete(model.id);
            ++this.selectedIncrement;
            return action;
        },

        /**
         * Clear all selected model
         *
         * @return {void}
         */
        clearSelected: function() {
            this.selected.clear();
            ++this.selectedIncrement;
        },

        /**
         * Remove model from gallery collection
         *
         * @param {Object} model [media model]
         * @return {Array} [removed array items]
         */
        destroyGalleryModel: function(model) {
            const index = this.source.findIndex((row) => row.id === model.id);
            return (index > -1) && this.source.splice(index, 1);
        },
    },

    computed: {
        headers() {
            return {
                'X-Requested-With': 'XMLHttpRequest',
            };
        },

        libraryData() {
            return this.library.data;
        },

        uploadPayload() {
            return {
                model_id: this.modelId,
                model_type: this.modelType,
                multiple: this.multiple,
                path: 'test-folder',
                entity: this.entity,
            };
        },

        galleryCollection() {
            if (!this.source) {
                return [];
            }
            if (_.isArray(this.source)) {
                return this.source;
            }
            return [this.source];
        },

        selectedCount() {
            return Object.values(this.selected).length;
        }
    },

    watch: {
        'state.view'(value) {
            if (value === false) {
                this.onClose();
            }
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
@import "~element-ui/packages/theme-chalk/src/link.scss";
@import "~element-ui/packages/theme-chalk/src/empty.scss";
@import "~element-ui/packages/theme-chalk/src/position.scss";

.sidebar {
    width: 280px;
    background: #f8f9fb;

    .el-overflow-container {
        padding: 20px 20px 0;
    }
}

.attachment-list {

}
</style>
