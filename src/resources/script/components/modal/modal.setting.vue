<template>
    <form method="post" @submit.prevent="handleSubmit">
        <div class="el-grid el-margin">
            <div class="el-width-1-3@md">
                <label class="el-text-bold2">ID:</label>
            </div>
            <div class="el-width-2-3@md">
                <span v-text="innerModel.id">-</span>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.key" class="el-text-bold2">Имя (ключ): *</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="setting.key" placeholder="заполните имя настройки" size="medium" prefix-icon="el-icon-key" v-model="innerModel.key" v-focus></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.name" class="el-text-bold2">Заголовок: *</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input id="setting.name" placeholder="заполните заголовок" size="medium" prefix-icon="el-icon-paragraph-left3" v-model="innerModel.name"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.description" class="el-text-bold2">Описание:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-input type="textarea" id="setting.description" placeholder="описание настройки" size="medium" v-model="innerModel.description"></el-input>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.group" class="el-text-bold2">Группа настроек:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-select id="setting.group" class="el-width-1-1" size="medium" v-model="innerModel.group_id" placeholder="Выберите группу настроек">
                    <i :class="currentGroup.icon" slot="prefix"></i>
                    <el-option v-for="group in groups" :key="group.id" :label="group.name" :value="group.id">
                        <i class="el-text-muted" :class="group.icon"></i><span class="el-margin-left" v-text="group.name"></span>
                    </el-option>
                </el-select>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.type" class="el-text-bold2">Элемент отображения:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-select id="setting.type" class="el-width-1-1" size="medium" v-model="innerModel.type" placeholder="Выберите элемент отображения">
                    <i :class="currentComponent.icon" slot="prefix"></i>
                    <el-option v-for="component in components" :key="component.name" :label="component.title" :value="component.name">
                        <div class="el-grid el-grid-sm el-flex el-flex-middle">
                            <div class="el-width-auto">
                                <i :class="component.icon"></i>
                            </div>
                            <div class="el-width-expand">
                                <div class="el-select-item_title" v-text="component.title"></div>
                                <div class="el-select-item_subtitle" v-text="component.name"></div>
                            </div>
                        </div>
                    </el-option>
                </el-select>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="setting.icon" class="el-text-bold2">Иконка:</label>
            </div>
            <div class="el-width-2-3@md">
                <el-icon-picker id="setting.icon" class="el-width-1-1" placeholder="Выберите иконку" size="medium" v-model="innerModel.icon"></el-icon-picker>
            </div>
        </div>

        <div class="el-grid el-flex el-flex-middle el-margin">
            <div class="el-width-1-3@md">
                <label for="menu.attribute" class="el-text-bold2">Дата создания:</label>
            </div>
            <div class="el-width-2-3@md">
                <pre class="" v-text="innerModel.created_at + ' / ' + innerModel.updated_at"></pre>
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
import {
    Button,
    IconPicker,
    Input,
    Link,
    MessageBox,
    Option,
    RadioButton,
    RadioGroup,
    Select,
    Switch,
} from 'element-ui';
import { transliterate } from '@dashboard/core/helper.js';
import ModelSetting from '@dashboard/models/setting.js';
import { postSetting } from '@dashboard/service/request/setting.js';
import { focus } from '@dashboard/directives';

export default {
    name: 'modal-setting',

    components: {
        ElButton: Button,
        ElIconPicker: IconPicker,
        ElInput: Input,
        ElLink: Link,
        ElOption: Option,
        ElRadioButton: RadioButton,
        ElRadioGroup: RadioGroup,
        ElSelect: Select,
        ElSwitch: Switch,
    },

    data() {
        return {
            innerModel: new ModelSetting(this.model),
            state: {
                isLoading: false,
            },
        }
    },

    props: {
        model: {
            type: Object,
        },
        groups: {
            type: Array,
            default: function() {
                return [];
            },
        },
        components: {
            type: Array,
            default: function() {
                return [];
            },
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
            return postSetting(this.innerModel, this.state).then((response) => {
                if (response.error === 200) {
                    this.$emit('input', this.innerModel);
                }
                if (response.error === 201) {
                    this.$emit('created', new ModelSetting(response.data));
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
        currentGroup() {
            return _.find(this.groups, {id: this.innerModel.group_id}) || {
                icon: 'el-icon-folder6',
            };
        },

        currentComponent() {
            return _.find(this.components, {name: this.innerModel.type}) || this.components[0];
        },
    },

    watch: {
        model(value) {
            this.innerModel = new ModelSetting(value);
        },
        'innerModel.key': function(value) {
            this.innerModel.key = transliterate(value, '_').toUpperCase();
        },
    },
}
</script>
