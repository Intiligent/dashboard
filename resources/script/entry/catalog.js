import Vue from 'vue';
import config from '@dashboard/config/app.js';
import {
    MessageBox,
    Tree,
    Switch,
    Link,
    Tooltip,
} from 'element-ui';
import { get } from 'lodash';
import {
    postCategory,
    putCategoryOrder,
    deleteCategory,
} from '@dashboard/service/request/catalog.js';
import ModalCategory from '@dashboard/components/modal/modal.category.vue';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElSwitch: Switch,
        ElLink: Link,
        ElTree: Tree,
        ElTooltip: Tooltip,
    },

    data: function() {
        return {
            tree: _.get(window, ['app', 'tree'], []),
        }
    },

    methods: {
        /**
         * Редактирования категории
         *
         * @param {Object} model [Menu model]
         * @param {Object} node [tree Node]
         * @return {Promise}
         */
        onCategoryEdit: function(model, node, parent) {
            const h = this.$createElement;
            return MessageBox({
                title: 'Категория',
                message: h(ModalCategory, {
                    props: {
                        model: model,
                        node: node,
                    },
                    on: {
                        input: (value) => {
                            node.data = value;
                        },
                        created: (value) => {
                            this.appendToNode(value, parent);
                        },
                        destroy: (model) => {
                            this.onDestroy(model, node).then((count) => {
                                if (count) MessageBox.close();
                            }).catch((error) => {
                                console.log('close');
                            });
                        },
                    },
                }),
                showCancelButton: false,
                showConfirmButton: false,
                showClose: true,
                size: 'medium',
            }).catch((state) => {
                console.log(state);
            });
        },

        onAppendNode: function(model, node) {
            let m = {
                parent_id: model ? model.id : null,
            };
            return this.onCategoryEdit(m, node, model);
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
                || model.slug.toLowerCase().indexOf(value.toLowerCase()) !== -1
                || model.id.toString().toLowerCase().indexOf(value.toLowerCase()) !== -1;
        },

        /**
         * Изменение активности
         *
         * @param  {Object} model [Menu model]
         * @param  {Boolean} value [value after changing]
         * @return {Promise}
         */
        onActiveChange: function(model, value) {
            let params = {id: model.id, active: value};
            return postCategory(params, this.state).then((response) => {
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
         * Перемещение элемента меню
         *
         * @param {Object} nodeDragging [Node start]
         * @param {Object} nodeDrop [Node end]
         * @param {String} eventType [event type: before / after / inner]
         * @param {Object} event [DragEvent]
         * @return {Promise}
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
            }
            return putCategoryOrder(params, this.state).then((response) => {
                // console.log(response);
            });
        },

        handleCommand(command) {
            if (command === 'add') {
                return this.onCategoryEdit({});
            }
        },

        /**
         * Добавление модели в дерево
         *
         * @param {Object} model [Category model]
         * @param {Object} parent [tree Node]
         * @return {Number} [tree length]
         */
        appendToNode: function(model, parent) {
            if (!parent) {
                return this.tree.push(model);
            }
            if (!parent.children) {
                this.$set(parent, 'children', []);
            }
            return parent.children.push(model);
        },

        /**
         * Удалить элемент из дерева (silent action)
         *
         * @param {Object} model [Category model]
         * @param {Object} node [tree Node]
         * @return {Promise}
         */
        destroy: function(model, node) {
            return deleteCategory({id: model.id}, this.state).then((response) => {
                const children = node.parent.childNodes;
                const index = children.indexOf(node);
                if (index < 0) {
                    throw new Error('undefined index');
                }
                return children.splice(index, 1).length;
            });
        },
    },

    watch: {
        q(value) {
            this.$refs.tree.filter(value);
        },
    },
});
