<template>
    <form method="post" @submit.prevent="submit">
        <div class="el-grid el-margin">
            <div class="el-width-1-4@md">
                <label class="el-text-bold2">ID:</label>
            </div>
            <div class="el-width-3-4@md">
                <span v-text="innerModel.id">-</span>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-4@md">
                <label for="model.name" class="el-text-bold2">Имя:</label>
            </div>
            <div class="el-width-3-4@md">
                <el-input id="model.name" placeholder="заполните имя категории" size="medium" prefix-icon="el-icon-paragraph-left3" v-model="innerModel.name"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-4@md">
                <label for="model.slug" class="el-text-bold2">Ссылка:</label>
            </div>
            <div class="el-width-3-4@md">
                <el-input id="model.slug" size="medium" prefix-icon="el-icon-link" placeholder="Заполните ссылку категории" v-model="innerModel.slug">
                    <el-button slot="append">Создать ссылку</el-button>
                </el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-1@md" style="margin-bottom: 8px;">
                <label for="model.description" class="el-text-bold2">Описание:</label>
            </div>
            <div class="el-width-1-1@md">
                <ckeditor id="model.description" v-model="innerModel.description" :config="editorConfig"></ckeditor>
                <!-- <el-input type="textarea" :rows="3" placeholder="Введите описание товара" v-model="model.description"></el-input> -->
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle">
            <div class="el-width-1-4@md">
                <label for="model.active" class="el-text-bold2">Родительская категория:</label>
            </div>
            <div class="el-width-3-4@md">
                <span v-text="parentName"></span>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle">
            <div class="el-width-1-4@md">
                <label for="model.active" class="el-text-bold2">Активность/видимость:</label>
            </div>
            <div class="el-width-3-4@md">
                <el-switch name="active" id="model.active" v-model="innerModel.active" active-color="#13ce66"></el-switch>
            </div>
        </div>

        <div class="el-grid el-grid-sm el-flex el-flex-middle el-margin-md">
            <div class="el-width-expand@md">
                <el-button size="medium" type="danger" icon="el-icon-cancel-circle2" @click="handleDestroy" plain v-if="innerModel.id">Удалить</el-button>
            </div>
            <div class="el-width-auto@md">
                <el-button size="medium" @click="handleCancel">Отмена</el-button>
            </div>
            <div class="el-width-auto@md">
                <el-button size="medium" native-type="submit" type="primary" :loading="state.isLoading">Сохранить</el-button>
            </div>
        </div>
    </form>
</template>

<script>
import Vue from 'vue';
import api from '@dashboard/core/api/index.js';
import ModelCategory from '@dashboard/models/category.js';
import CKEditor from 'ckeditor4-vue';
import {
    Input,
    Button,
    Switch,
    MessageBox,
} from 'element-ui';
import {
    postCategory,
} from '@dashboard/service/request/catalog.js';

export default {
    name: 'modal-category',

    components: {
        ElInput: Input,
        ElButton: Button,
        ElSwitch: Switch,
        ckeditor: CKEditor.component,
    },

    data() {
        return {
            innerModel: new ModelCategory(this.model),
            state: {
                isLoading: false,
            },
            editorConfig: {
                height: 150,
                toolbarGroups: [
                    { name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                    { name: 'clipboard', groups: ['clipboard', 'undo']},
                    { name: 'links'},
                    { name: 'paragraph', groups: ['list', 'blocks', 'align', 'bidi']},
                    { name: 'document', groups: ['mode', 'document', 'doctools']},
                    { name: 'styles'},
                    { name: 'colors'},
                    { name: 'others'},
                    { name: 'tools'},
                ],
            },
        }
    },

    props: {
        // модель категории
        model: {
            type: Object,
        },
        // tree Node
        node: {
            type: Object,
        },
    },

    model: {
        prop: 'model',
        event: 'input',
    },

    methods: {
        /**
         * Отправка формы
         *
         * @param {Object} event [SubmitEvent]
         * @return {Promise}
         */
        submit: function(event) {
            return postCategory(this.innerModel, this.state).then((response) => {
                if (response.error === 200) {
                    this.$emit('input', this.innerModel);
                }
                if (response.error === 201) {
                    this.$emit('created', response.data);
                }
                this.handleCancel();
            });
        },

        /**
         * Закрытие модального окна
         *
         * @return {void}
         */
        handleCancel: function() {
            MessageBox.close();
        },

        /**
         * Удаление элемента меню
         *
         * @param {Object} event [ClickEvent]
         * @return {Promise}
         */
        handleDestroy: function(event) {
            this.$emit('destroy', this.innerModel);
        },
    },

    computed: {
        parentName() {
            if (!this.node || this.node.parent.level === 0) {
                return 'root';
            }
            return this.node.parent.data.name;
        },
    },

    watch: {
        model(value) {
            this.innerModel = new ModelCategory(value);
        },
    },
}
</script>
