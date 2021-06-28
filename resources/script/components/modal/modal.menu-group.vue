<template>
    <form method="post" @submit.prevent="handleSubmit">
        <div class="el-grid el-margin" v-if="innerModel.id">
            <div class="el-width-1-3@md">
                <label class="el-text-bold2">ID:</label>
            </div>
            <div class="el-width-2-3@md">
                <span v-text="innerModel.id">-</span>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="model.name" class="el-text-bold2">Заголовок: *</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="model.name" placeholder="заполните заголовок меню" size="medium" prefix-icon="el-icon-paragraph-left3" v-model="innerModel.name" v-focus></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="model.code" class="el-text-bold2">Системное имя: *</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="model.code" placeholder="Введите имя категории" size="medium" prefix-icon="el-icon-circle-code" v-model="innerModel.code"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle">
            <div class="el-width-1-3@md">
                <label for="model.active" class="el-text-bold2">Активность/видимость:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-switch id="model.active" v-model="innerModel.active" active-color="#13ce66"></el-switch>
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
import api from '@/core/api/index.js';
import {
    Input,
    Button,
    Switch,
    MessageBox,
} from 'element-ui';
import { transliterate } from '@/core/helper.js';
import { focus } from '@dashboard/directives';
import ModelMenu from '@dashboard/models/menu.js';
import { postMenuGroup } from '@dashboard/service/request/menu.js';

export default {
    name: 'modal-menu-group',

    components: {
        ElInput: Input,
        ElButton: Button,
        ElSwitch: Switch,
    },

    data() {
        return {
            innerModel: new ModelMenu(this.model),
            state: {
                isLoading: false,
            },
        }
    },

    props: {
        model: {
            type: Object,
            default: function() {
                return {};
            },
        },
    },

    // методы
    methods: {

        /**
         * отправка формы
         *
         * @param event - событие нажатия кнопки
         * @return Promise
         */
        handleSubmit: function(event) {
            return postMenuGroup(this.innerModel, this.state).then((response) => {
                if (response.error === 200) {
                    this.$emit('input', this.innerModel);
                }
                if (response.error === 201) {
                    this.$emit('created', new ModelMenu(response.data));
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

    watch: {
        model(value) {
            this.innerModel = new ModelMenu(value);
        },
        'innerModel.name'(value, oldValue) {
            this.innerModel.code = transliterate(value).toLowerCase();
        },
    },
}
</script>
