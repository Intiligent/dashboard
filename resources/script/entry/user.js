import Vue from 'vue';
import {
    Avatar,
    ButtonGroup,
    Breadcrumb,
    BreadcrumbItem,
    Table,
    TableColumn,
    Tag,
    Tooltip,
} from 'element-ui';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import ModelUser from '@dashboard/models/user.js';
import { getUsers, postUser } from '@dashboard/service/request/user.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElAvatar: Avatar,
        ElButtonGroup: ButtonGroup,
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElTag: Tag,
        ElTooltip: Tooltip,
    },

    data: function() {
        return {
            model: new ModelUser(_.get(window.app, 'model')),
            collection: _.get(window, ['app', 'collection']),
        }
    },

    methods: {
        /**
         * Отправка формы
         *
         * @param {Object} event [SubmitEvent]
         * @return {Promise}
         */
        handleSubmit: function(event) {
            return postUser(this.model, this.state).then((response) => {
                if (response.error === 201) {
                    window.location = route('dashboard.userList');
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
            return getUsers(payload).then((response) => {
                this.collection = response.data;
                this.$router.push({query: payload}).catch((error) => {
                    console.log(error);
                });
            });
        },
    },

    computed: {
        tableRows() {
            if (this.q) {
                return this.collection.data.filter((row) => {
                    return row.name.toLowerCase().indexOf(this.q) !== -1
                        || row.email.toLowerCase().indexOf(this.q) !== -1
                        || row.phone.toLowerCase().indexOf(this.q) !== -1;
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
