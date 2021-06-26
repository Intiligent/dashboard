import Vue from 'vue';
import config from '@dashboard/config/app.js';
import {
    ButtonGroup,
    IconPicker,
    Link,
    MessageBox,
    Switch,
    TabPane,
    Tabs,
    Tag,
    Tree,
    Tooltip,
} from 'element-ui';
import ModalMenu from '@dashboard/components/modal/modal.menu.vue';
import ModalMenuGroup from '@dashboard/components/modal/modal.menu-group.vue';
import { TYPE_URI } from '@dashboard/config/const.js';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import { postMenu, putMenuOrder, deleteMenu } from '@dashboard/service/request/menu.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElButtonGroup: ButtonGroup,
        ElIconPicker: IconPicker,
        ElLink: Link,
        ElMessageBox: MessageBox,
        ElTabPane: TabPane,
        ElTag: Tag,
        ElTabs: Tabs,
        ElTree: Tree,
        ElSwitch: Switch,
        ElTooltip: Tooltip,
    },

    data: function() {
        let currentTabName = this.$route.hash.substring(1) || _.get(window.app, ['tree', 0, 'code']);
        return {
            currentTabName: currentTabName,
            tree: _.get(window, ['app', 'tree'], []),
            groups: _.get(window, ['app', 'groups'], []),
            routes: _.get(window, ['app', 'routes'], []),
        }
    },

    methods: {
        /**
         * Окно редактирования меню
         *
         * @param {Object} model [Menu model]
         * @param {Object} node [Node]
         * @param {Object} parent [Menu model parent]
         * @return {Promise}
         */
        syncMenu: function({model, node, parent} = {}) {
            const h = this.$createElement;
            return MessageBox({
                title: 'Меню',
                message: h(ModalMenu, {
                    props: {
                        model: model || {parent_id: parent.id},
                        groups: this.groups,
                        routes: this.routes,
                    },
                    on: {
                        input: (value) => {
                            this.onMenuUpdate(model, value, node)
                        },
                        created: (value) => {
                            this.appendToNode(value, parent);
                        },
                        destroy: (model) => {
                            this.onDestroy(model, node).then((count) => {
                                if (count) {
                                    MessageBox.close();
                                }
                            });
                        },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                customClass: 'el-modal-small',
            }).catch((state) => {
                console.log('action:', state);
            });
        },

        /**
         * Окно редактирования группы меню
         *
         * @param {Object} model [Menu model]
         * @param {Object} node [Node]
         * @param {Object} parent [Menu model parent]
         * @return {Promise}
         */
        syncMenuGroup: function({model, node, parent} = {}) {
            const h = this.$createElement;
            return MessageBox({
                title: 'Группа меню',
                message: h(ModalMenuGroup, {
                    props: {
                        model: model,
                    },
                    on: {
                        input: (value) => {
                            this.onMenuGroupUpdate(model, value);
                        },
                        created: (value) => {
                            this.appendToNode(value, parent);
                            this.currentTabName = value.code;
                        },
                        destroy: (model) => {
                            this.onDestroy(model, node).then((count) => {
                                if (count) {
                                    this.currentTabName = _.get(this.tree, [0, 'code']);
                                    MessageBox.close();
                                }
                            });
                        },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                customClass: 'el-modal-small',
            }).catch((state) => {
                console.log('action:', state);
            });
        },

        /**
         * Actions в меню
         *
         * @param {String} command [action name]
         * @return {void}
         */
        handleMenuCommand: function(command) {
            if (command === 'addgroup') {
                this.syncMenuGroup();
            }
        },

        /**
         * Изменение активности меню
         *
         * @param  {Object} model [Menu model]
         * @param  {Boolean} value [value after changing]
         * @return {Promise}
         */
        onActiveChange: function(model, value) {
            let params = {id: model.id, active: value};
            return postMenu(params, this.state).then((response) => {
                console.log(response);
            });
        },

        /**
         * Удалить элемент из дерева (запрос на удаление)
         *
         * @param {Object} model [Category model]
         * @param {Object} node [tree Node]
         * @return {Promise}
         */
        onDestroy: function(model, node) {
            if (!confirm('Вы действительно хотите удалить: ' + model.name)) {
                return Promise.resolve();
            }
            return this.destroy(model, node);
        },

        /**
         * Will be executed on each node when use filter
         * if return false, tree node will be hidden
         *
         * @param {String} value [Query string]
         * @param {Object} model [Category model]
         * @param {Object} node [Node of tree]
         * @return {Boolean}
         */
        onFilterNode: function(value, model, node) {
            if (!value) return true;
            return model.name.toLowerCase().indexOf(value.toLowerCase()) !== -1
                || model.value.toLowerCase().indexOf(value.toLowerCase()) !== -1
                || model.id.toString().toLowerCase().indexOf(value.toLowerCase()) !== -1;
        },

        /**
         * Перемещение элемента меню
         *
         * @param {Object} nodeDragging [Node start]
         * @param {Object} nodeDrop [Node end]
         * @param {String} eventType [event type: before / after / inner]
         * @param {Object} event [DragEvent]
         */
        onNodeDrop: function(nodeDragging, nodeDrop, eventType, event) {
            let params = {id: nodeDragging.data.id, event: eventType};
            if (eventType === 'inner') {
                params.parent_id = nodeDrop.data.id;
            }
            if (['before', 'after'].includes(eventType)) {
                let startIndex = Array.prototype.indexOf.call(event.target.parentNode.childNodes, event.target);
                let endIndex = nodeDrop.parent.childNodes.findIndex((node) => node.data.id === nodeDragging.data.id);
                params.offset = Math.abs(startIndex - endIndex);
                if (nodeDragging.level !== nodeDrop.level) {
                    params.event = 'inner';
                    params.parent_id = nodeDrop.parent.data.id;
                    if (endIndex > 0) params.offset = endIndex;
                }
                if (nodeDragging.data.parent_id !== nodeDrop.data.parent_id) {
                    params.event = 'inner';
                    params.parent_id = nodeDrop.data.parent_id;
                }
            }
            putMenuOrder(params, this.state).then((response) => {
                // console.log(response);
            });
        },

        /**
         * Добавление вложеного элемента
         *
         * @param {Object} model [Menu model]
         * @param {Object} node [Node]
         * @return {Promise}
         */
        onAppendNode: function(model, node) {
            let current = {parent_id: model ? model.id : null};
            return this.syncMenu({model: current, node, parent: model});
        },

        /**
         * Обновление группы меню
         *
         * @param {Object} oldValue [ModelMenu]
         * @param {Object} newValue [ModelMenu]
         * @return {void}
         */
        onMenuGroupUpdate: function(oldValue, newValue) {
            if (oldValue.code !== newValue.code) {
                this.currentTabName = newValue.code;
            }
            Object.assign(oldValue, newValue);
        },

        /**
         * Обновление меню
         *
         * @param {Object} oldValue [ModelMenu]
         * @param {Object} newValue [ModelMenu]
         * @return {void}
         */
        onMenuUpdate: function(oldValue, newValue, node = null) {
            if (oldValue.parent_id !== newValue.parent_id) {
                const newGroup = this.groups.find((group) => group.id === newValue.parent_id);
                const destinationTree = this.$refs[newGroup.code][0];
                const parentNode = destinationTree.getNode(newValue.parent_id);
                destinationTree.append(newValue, parentNode);
                const currentTree = this.$refs[this.currentTabName][0];
                currentTree.remove(newValue.id);
            }
            Object.assign(oldValue, newValue);
        },

        /**
         * Добавление модели в дерево
         *
         * @param {Object} model [Menu model]
         * @param {Object} parent [tree Node]
         * @return {Number} [tree length]
         */
        appendToNode: function(model, parent) {
            if (parent) {
                return parent.children.push(model);
            }
            let currentGroupIndex = _.findIndex(this.tree, (item) => {
                return item.code === model.code; // this.currentTabName
            });
            if (currentGroupIndex >= 0) {
                return this.tree[currentGroupIndex].children.push(model);
            }
            return this.tree.push(model);
        },

        /**
         * Удалить элемент из дерева (silent action)
         *
         * @param {Object} model [Menu model]
         * @param {Object} node [tree Node]
         * @return {Promise}
         */
        destroy: function(model, node) {
            return deleteMenu({id: model.id}, this.state).then((response) => {
                const children = node ? node.parent.childNodes : this.tree;
                const index = node ? children.indexOf(node) : children.findIndex((row) => row.id === model.id);
                return index >= 0 ? children.splice(index, 1).length : 0;
            });
        },
    },

    watch: {
        q(value) {
            this.$refs[this.currentTabName][0].filter(value);
        },
        currentTabName(value, oldValue) {
            this.$router.push({hash: '#' + value}).catch((error) => {
                console.log('message', error.message);
            });
            if (this.q) {
                this.$refs[value][0].filter(this.q);
            }
        },
    },
});
