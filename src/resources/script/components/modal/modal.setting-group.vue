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
                <el-input id="model.name" placeholder="заполните заголовок" size="medium" prefix-icon="el-icon-paragraph-left3" v-model="innerModel.name" v-focus></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="model.description" class="el-text-bold2">Описание:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input type="textarea" id="model.description" placeholder="описание группы настройки" size="medium" v-model="innerModel.description"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="model.icon" class="el-text-bold2">Иконка:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-icon-picker id="model.icon" class="el-width-1-1" placeholder="Выберите иконку для группы" size="medium" v-model="innerModel.icon"></el-icon-picker>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label class="el-text-bold2">Дата создания:</label>
            </div>
            <div class="el-width-2-3@md">
                <pre class="" v-text="innerModel.created_at + ' / ' + innerModel.updated_at"></pre>
            </div>
        </div>

        <div class="el-grid el-grid-sm el-flex el-flex-middle el-margin-md-top">
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
import {
    Button,
    IconPicker,
    Input,
    Link,
    MessageBox,
} from 'element-ui';
import { focus } from '@dashboard/directives';
import ModelSettingGroup from '@dashboard/models/setting-group.js';
import { postSettingGroup } from '@dashboard/service/request/setting.js';

export default {
    name: 'modal-setting-group',

    components: {
        ElButton: Button,
        ElIconPicker: IconPicker,
        ElInput: Input,
        ElLink: Link,
    },

    data() {
        return {
            innerModel: new ModelSettingGroup(this.model),
            state: {
                isLoading: false,
            },
        }
    },

    props: {
        model: {
            type: Object,
        },
    },

    methods: {
        /**
         * Отправка формы
         *
         * @param {Object} event [Form submit]
         * @return {Promise}
         */
        handleSubmit: function(event) {
            return postSettingGroup(this.innerModel, this.state).then((response) => {
                if (response.error === 200) {
                    this.$emit('input', this.innerModel);
                }
                if (response.error === 201) {
                    this.$emit('created', new ModelSettingGroup(response.data));
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
            this.innerModel = new ModelSettingGroup(value);
        },
        'innerModel.name'(value, oldValue) {
            // this.model.key = transliterate(value).toUpperCase();
        },
    },
}
</script>
