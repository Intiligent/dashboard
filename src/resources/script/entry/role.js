import Vue from 'vue';
import {
    Breadcrumb,
    BreadcrumbItem,
    ButtonGroup,
    CheckboxGroup,
    Checkbox,
    Link,
    Switch,
    Tag,
    Table,
    TableColumn,
} from 'element-ui';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import {
    postRole,
} from '@dashboard/service/request/acl.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElButtonGroup: ButtonGroup,
        ElCheckboxGroup: CheckboxGroup,
        ElCheckbox: Checkbox,
        ElLink: Link,
        ElSwitch: Switch,
        ElTag: Tag,
        ElTable: Table,
        ElTableColumn: TableColumn,
    },

    data() {
        return {
            collection: _.get(window.app, 'collection', []),
            role: Object.assign({
                id: null,
                name: null,
            }, _.get(window.app, 'role')),
        };
    },

    methods: {
        /**
         * отправка формы
         *
         * @param {Object} event [SubmitEvent]
         * @return Promise
         */
        handleSubmit: function(event) {
            return postRole(this.role, this.state).then((response) => {
                if (response.error === 201) {
                    window.location = route('dashboard.roles');
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
                this.onDuplicate(this.role);
            }
            if (command === 'destroy') {
                this.onDestroy(this.role, {redirect: true});
            }
        },

        /**
         * Дублировато текстовую страницу
         *
         * @param {Object} role [Role model]
         * @return {Promise}
         */
        onDuplicate: function(role) {
            return postArticleDuplicate(role).then((response) => {
                window.location = route('dashboard.role', {id: response.data.id});
            });
        },

        /**
         * Запрос на удаление
         *
         * @param {Object} role [Role model]
         * @param {Object} params [{redirect}]
         * @return {Promise}
         */
        onDestroy: function(role, {redirect} = {redirect: false}) {
            if (!confirm(`Вы действительно хотите удалить: ${role.title}`)) {
                return Promise.resolve();
            }
            return this.destroy(role).then((count) => {
                if (count) {
                    this.collection.total -= 1;
                }
                if (count && redirect) {
                    window.location = route('dashboard.articleList');
                }
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
         * Удалить элемент
         *
         * @param {Object} role [Role model]
         * @return {Promise}
         */
        destroy: function(role) {
            return deleteArticle(role, this.state).then((response) => {
                const index = this.collection.data.findIndex((row) => row.id === role.id);
                if (index >= 0) {
                    return this.collection.data.splice(index, 1).length;
                }
                return 1;
            });
        },
    },

    computed: {
        tableRows() {
            if (this.q) {
                return this.collection.data.filter((row) => {
                    return row.title.toLowerCase().indexOf(this.q) !== -1
                        || row.name.toLowerCase().indexOf(this.q) !== -1;
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
