import Vue from 'vue';
import {
    InputNumber,
    Link,
    Message,
    MessageBox,
    RadioButton,
    RadioGroup,
    Switch,
    Tabs,
    TabPane,
    Tag,
    Tooltip,
} from 'element-ui';
import Clipboard from 'clipboard';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import ModalSetting from '@dashboard/components/modal/modal.setting.vue';
import ModalSettingGroup from '@dashboard/components/modal/modal.setting-group.vue';
import { putSettings, deleteSetting, deleteSettingGroup } from '@dashboard/service/request/setting.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElInputNumber: InputNumber,
        ElLink: Link,
        ElRadioButton: RadioButton,
        ElRadioGroup: RadioGroup,
        ElSwitch: Switch,
        ElTabs: Tabs,
        ElTabPane: TabPane,
        ElTag: Tag,
        ElTooltip: Tooltip,
        ModalSetting,
    },

    data: function() {
        let currentTab = this.$route.hash && this.$route.hash.toString().replace(/^#/, '')
            || window.app.settings[0].code;

        return {
            q: null,
            currentGroupName: currentTab,
            settings: window.app.settings,
        }
    },

    mounted: function() {
        this.clipboard = new Clipboard('.clipboard-copy');
        this.clipboard.on('success', function(event) {
            Message({
                message: 'Success copy data to clipboard',
                type: 'success',
            });
        });
    },

    created: function() {
        this.components = window.app.components;
        this.RADIO_GROUP = 'radio-group';
    },

    destroyed() {
        this.clipboard.off('success');
    },

    methods: {
        /**
         * Отправка формы сохранения
         *
         * @param {Object} event [SubmitForm]
         * @param {Object} group [SettingGroup model]
         * @return {void}
         */
        onSubmit: function(event, group) {
            const payload = _.chain(group.settings)
                .map((model) => _.pick(model, ['id', 'group_id', 'name', 'key', 'value']))
                .value();

            return putSettings(payload, this.state).then((response) => {
                console.log('response', response);
            });
        },

        /**
         * Окно редактирования настройки
         *
         * @param {Object} group [SettingGroup model]
         * @param {Object} model [Setting model]
         * @return {Promise}
         */
        syncSetting: function(group, model) {
            const h = this.$createElement;
            if (!model) {
                model = {
                    group_id: group.id,
                };
            }
            return MessageBox({
                title: 'Настройки',
                message: h(ModalSetting, {
                    props: {
                        model: model,
                        groups: this.settings,
                        components: this.components,
                    },
                    on: {
                        input: (value) => {
                            this.onUpdate(model, value);
                        },
                        created: (value) => {
                            group.settings.push(value);
                        },
                        destroy: (model) => {
                            this.onDestroy(group, model).then((count) => {
                                if (count) {
                                    MessageBox.close();
                                }
                            }).catch((error) => {
                                console.log('close');
                            });
                        },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                size: 'small',
            }).catch((state) => {
                console.log(state);
            });
        },

        /**
         * Создание/Редактирование группы
         *
         * @param {Object} model [SettingGroup]
         * @return void
         */
        syncSettingGroup: function(model = null) {
            const h = this.$createElement;
            return MessageBox({
                title: 'Настройки группы',
                message: h(ModalSettingGroup, {
                    props: {
                        model: model,
                    },
                    on: {
                        input: (value) => {
                            Object.assign(model, value);
                        },
                        created: (value) => {
                            this.settings.push(value);
                            this.currentGroupName = value.code;
                        },
                        destroy: (model) => {
                            this.onGroupDestroy(model).then((count) => {
                                if (count) {
                                    this.currentGroupName = this.settings[0].code;
                                    MessageBox.close();
                                }
                            }).catch((error) => {
                                console.log('close');
                            });
                        },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                size: 'small',
            }).catch((state) => {
                console.log(state);
            });
        },

        /**
         * Удалить элемент из дерева (запрос на удаление)
         *
         * @param {Object} group [SettingGroup model]
         * @param {Object} model [Setting model]
         * @return {Promise}
         */
        onDestroy: function(group, model) {
            if (!confirm('Вы действительно хотите удалить: ' + model.key)) {
                return Promise.resolve();
            }
            return this.destroy(group, model);
        },

        /**
         * Удалить элемент из дерева (запрос на удаление)
         *
         * @param {Object} model [SettingGroup]
         * @return {Promise}
         */
        onGroupDestroy: function(model) {
            if (!confirm('Вы действительно хотите удалить: ' + model.name)) {
                return Promise.resolve();
            }
            return this.destroyGroup(model);
        },

        /**
         * Обновление данных настройки
         *
         * @param {Object} oldModel [SettingModel]
         * @param {Object} newModel [SettingModel]
         * @return {void}
         */
        onUpdate: function(oldModel, newModel) {
            // перемещение между группами
            if (oldModel.group_id !== newModel.group_id) {
                const groupIndexFrom = this.settings.findIndex((row) => row.id === oldModel.group_id);
                const groupSettingsFrom = _.get(this.settings, [groupIndexFrom, 'settings'], []);
                const modelIndexFrom = groupSettingsFrom.findIndex((row) => row.id === oldModel.id);
                if (modelIndexFrom >= 0) {
                    groupSettingsFrom.splice(modelIndexFrom, 1);
                }
                const groupIndexTo = this.settings.findIndex((row) => row.id === newModel.group_id)
                const groupSettingsTo = _.get(this.settings, [groupIndexTo, 'settings']);
                if (groupSettingsTo) {
                    groupSettingsTo.push(newModel);
                }
                const codeTo = _.get(this.settings, [groupIndexTo, 'code']);
                this.currentGroupName = codeTo;
            }
            Object.assign(oldModel, newModel);
        },

        /**
         * Удалить элемент из дерева (silent action)
         *
         * @param {Object} group [SettingGroup]
         * @param {Object} model [Setting]
         * @return {Promise}
         */
        destroy: function(group, model) {
            return deleteSetting({id: model.id}, this.state).then((response) => {
                const children = group.settings;
                const index = children.findIndex((row) => row.id === model.id);
                return index >= 0 ? children.splice(index, 1).length : 0;
            });
        },

        /**
         * Удалить элемент из дерева (silent action)
         *
         * @param {Object} model [SettingGroup]
         * @return {Promise}
         */
        destroyGroup: function(model) {
            return deleteSettingGroup({id: model.id}, this.state).then((response) => {
                const children = this.settings;
                const index = children.indexOf(model);
                return children.splice(index, 1).length;
            });
        },
    },

    computed: {
        //
    },

    watch: {
        currentGroupName: function(value) {
            if (value) {
                this.$router.push({hash: '#' + value});
            }
        },
    },
});
