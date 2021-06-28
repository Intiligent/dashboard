<template>
    <form method="post" class="el-margin-top2" @submit.prevent="submit">
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
                <label for="menu.name" class="el-text-bold2">Заголовок: *</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="menu.name" placeholder="заполните заголовок меню" size="medium" prefix-icon="el-icon-paragraph-left3" v-model="innerModel.name" v-focus></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="menu.type" class="el-text-bold2">Тип ссылки:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-radio-group size="medium" v-model="innerModel.type">
                    <el-radio-button :label="type.key" v-for="type in types" :key="type.key">
                        <i :class="type.icon"></i>
                        <span v-text="type.name"></span>
                    </el-radio-button>
                </el-radio-group>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <a class="el-link-out" target="_blank" href="goAhead">перейти по ссылке</a>
            </div>
            <div class="el-width-2-3@md">
                <template v-if="innerModel.type === 'uri'">
                    <el-input placeholder="Введите ссылку" size="medium" v-model="innerModel.value">
                        <template slot="prepend"><i class="el-icon-link"></i></template>
                    </el-input>
                </template>
                <template v-if="innerModel.type === 'route'">
                    <el-select class="el-width-1-1" size="medium" clearable filterable v-model="innerModel.value" placeholder="Выберите маршрут ссылки">
                        <template slot="prefix"><i class="el-icon-link"></i></template>
                        <el-option v-for="route in routes" :key="route.name" :label="route.name" :value="route.name">
                            <span style="float: left" v-text="route.name"></span>
                            <span style="float: right; color: #8492a6; font-size: 13px" v-text="route.uri"></span>
                        </el-option>
                    </el-select>
                </template>
                <template v-if="innerModel.type === 'article'">
                    <el-input placeholder="Выберите текстовую страницу" size="medium" v-model="innerModel.value">
                        <template slot="prepend"><i class="el-icon-bookmark"></i></template>
                    </el-input>
                </template>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="menu.icon">Иконка:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-icon-picker id="menu.icon" class="el-width-1-1" placeholder="Выберите иконку" size="medium" v-model="innerModel.icon"></el-icon-picker>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="menu.attribute">Атрибуты:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="menu.attribute" placeholder="html атрибуты ссылки" prefix-icon="el-icon-code" size="medium" v-model="innerModel.attribute"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="menu.group">Группа меню:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-select class="el-width-1-1" size="medium" clearable v-model="innerModel.parent_id" placeholder="Выберите группу меню">
                    <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id"></el-option>
                </el-select>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle">
            <div class="el-width-1-3@md">
                <label for="menu.active">Активность:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-switch v-model="innerModel.active" active-color="#13ce66"></el-switch>
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
    RadioGroup,
    RadioButton,
    Select,
    Option,
    Switch,
    IconPicker,
    MessageBox,
} from 'element-ui';
import {
    TYPE_URI,
    TYPE_ROUTE,
    TYPE_ARTICLE,
} from '@dashboard/config/const.js';
import { focus } from '@dashboard/directives';
import ModelMenu from '@dashboard/models/menu.js';
import { postMenu } from '@dashboard/service/request/menu.js';

export default {
    name: 'modal-menu',

    components: {
        ElInput: Input,
        ElButton: Button,
        ElRadioGroup: RadioGroup,
        ElRadioButton: RadioButton,
        ElSelect: Select,
        ElOption: Option,
        ElSwitch: Switch,
        ElIconPicker: IconPicker,
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
        // модель меню
        model: {
            type: Object,
        },
        // группы меню
        groups: {
            type: Array,
            default: function() {
                return [];
            },
        },
        // список роутов
        routes: {
            type: Array,
            default: function() {
                return [];
            },
        },
    },

    created() {
        this.types = [
            {key: TYPE_URI, name: 'Ссылка', icon: 'el-icon-link el-icon-08x'},
            {key: TYPE_ROUTE, name: 'Маршрут', icon: 'el-icon-merge el-icon-08x'},
            {key: TYPE_ARTICLE, name: 'Страница текст.', icon: 'el-icon-bookmark el-icon-08x'},
        ];
    },

    methods: {
        /**
         * Отправка формы
         *
         * @param {Object} event [SubmitForm]
         * @return {Promise}
         */
        submit: function(event) {
            return postMenu(this.innerModel, this.state).then((response) => {
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
    },
}
</script>
