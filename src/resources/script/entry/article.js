import Vue from 'vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    ButtonGroup,
    Link,
    Switch,
    Tag,
    Table,
    TableColumn,
} from 'element-ui';
import { transliterate } from '@dashboard/core/helper.js';
import { EDITOR_CONFIG_DEFAULT } from '@dashboard/config/constant.js';
import CKEditor from 'ckeditor4-vue';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import ModelArticle from '@dashboard/models/article.js';
import ElMediaLibrary from '@dashboard/components/media-library';
import {
    getArticles,
    postArticle,
    postArticleDuplicate,
    deleteArticle,
} from '@dashboard/service/request/article.js';

Vue.use(CKEditor);

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElButtonGroup: ButtonGroup,
        ElLink: Link,
        ElSwitch: Switch,
        ElTag: Tag,
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElMediaLibrary,
    },

    data: function() {
        return {
            collection: _.get(window.app, 'collection', []),
            model: new ModelArticle(_.get(window.app, 'model')),
            editorConfig: EDITOR_CONFIG_DEFAULT,
        }
    },

    methods: {
        /**
         * отправка формы
         *
         * @param {Object} event [SubmitEvent]
         * @return Promise
         */
        handleSubmit: function(event) {
            return postArticle(this.model, this.state).then((response) => {
                if (response.error === 201) {
                    window.location = route('dashboard.articleList');
                }
            });
        },

        /**
         * Команды в меню
         *
         * @param {String} command [copy|destroy]
         * @return {void}
         */
        handleMenuCommand: function(command) {
            if (command === 'copy') {
                this.onDuplicate(this.model);
            }
            if (command === 'destroy') {
                this.onDestroy(this.model, {redirect: true});
            }
        },

        /**
         * Дублировато текстовую страницу
         *
         * @param {Object} model [Article model]
         * @return {Promise}
         */
        onDuplicate: function(model) {
            return postArticleDuplicate(model).then((response) => {
                window.location = route('dashboard.article', {id: response.data.id});
            });
        },

        /**
         * Запрос на удаление
         *
         * @param {Object} model [Article model]
         * @param {Object} params [{redirect}]
         * @return {Promise}
         */
        onDestroy: function(model, {redirect} = {redirect: false}) {
            if (!confirm(`Вы действительно хотите удалить: ${model.title}`)) {
                return Promise.resolve();
            }
            return this.destroy(model).then((count) => {
                if (count) {
                    this.collection.total -= 1;
                }
                if (count && redirect) {
                    window.location = route('dashboard.articleList');
                }
            });
        },

        /**
         * Изменение активности
         *
         * @param {Object} model [Article model]
         * @param {Boolean} value [current value]
         * @return {Promise}
         */
        onActiveChange: function(model, value) {
            let params = {id: model.id, active: value};
            return postArticle(params, this.state).then((response) => {
                // console.log(response);
            });
        },

        /**
         * Сортировка таблицы
         *
         * @param {Object} column [колонка]
         * @param {String} prop [имя колонки]
         * @param {String} order [ascending|descending]
         * @return {Promise}
         */
        onSortChange: function({column, prop: columnName, order}) {
            const direction = order === 'ascending' ? 'asc' : order === 'descending' ? 'desc' : null;
            const payload = {sort: columnName, direction};
            return getArticles(payload).then((response) => {
                this.collection = response.data;
                this.$router.push({query: payload}).catch((error) => {
                    console.log(error);
                });
            });
        },

        /**
         * Класс для строк таблиц
         *
         * @param {Object} row [Article model]
         * @param {Number} rowIndex [index]
         * @return {String}
         */
        tableRowClassName({row, rowIndex}) {
            const className = [];
            if (!row.active) {
                className.push('inactive-row');
            }
            return className;
        },

        /**
         * Удалить элемент
         *
         * @param {Object} model [Article model]
         * @return {Promise}
         */
        destroy: function(model) {
            return deleteArticle(model, this.state).then((response) => {
                const index = this.collection.data.findIndex((row) => row.id === model.id);
                if (index >= 0) {
                    return this.collection.data.splice(index, 1).length;
                }
                return 1;
            });
        },

        generateSlug: function(event) {
            this.model.slug = transliterate(this.model.title);
        },
    },

    computed: {
        tableRows() {
            if (this.q) {
                return this.collection.data.filter((row) => {
                    return row.title.toLowerCase().indexOf(this.q) !== -1
                        || row.slug.toLowerCase().indexOf(this.q) !== -1;
                });
            }
            return this.collection.data;
        },

        defaultSort() {
            const prop = this.$route.query.sort;
            if (prop) {
                const direction = this.$route.query.direction;
                const order = direction === 'desc' ? 'descending' : (direction === 'asc' ? 'ascending' : null);
                return {prop, order};
            }
        },
    },
});
