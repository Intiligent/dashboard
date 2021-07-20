import Vue from 'vue';
import { get } from 'lodash';
import {
    ButtonGroup,
    Switch,
    Tag,
    InputNumber,
    Select,
    Option,
    Breadcrumb,
    BreadcrumbItem,
    Table,
    TableColumn,
    Tooltip,
    Slider,
    Drawer,
    CheckboxGroup,
    Checkbox,
    Pagination,
    Avatar,
    Autocomplete,
} from 'element-ui';
import {
    getProductSuggest,
    postProduct,
    deleteProduct,
} from '@dashboard/service/request/product.js';
import ModelProduct from '@dashboard/models/product.js';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElButtonGroup: ButtonGroup,
        ElSwitch: Switch,
        ElTag: Tag,
        ElInputNumber: InputNumber,
        ElSelect: Select,
        ElOption: Option,
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElTooltip: Tooltip,
        ElSlider: Slider,
        ElDrawer: Drawer,
        ElCheckboxGroup: CheckboxGroup,
        ElCheckbox: Checkbox,
        ElPagination: Pagination,
        ElAvatar: Avatar,
        ElAutocomplete: Autocomplete,
    },

    data: function() {
        return {
            // поисковый запрос
            q: this.$router.currentRoute.query.q,
            product: new ModelProduct(_.get(window, ['app', 'product'])),
            collection: _.get(window, ['app', 'collection']),
            categories: _.get(window, ['app', 'categories'], []),
            drawer: {
                filter: false,
            },
            filters: {
                category_id: [],
                price: [0, 300],
                active: [],
            },
        }
    },

    methods: {
        /**
         * Отправка формы
         *
         * @param event - событие нажатия кнопки
         * @return Promise
         */
        submit: function(event) {
            return postProduct(this.product, this.state).then((response) => {
                if (response.error === 201) {
                    window.location = route('dashboard.productList');
                }
            });
        },

        /**
         * Удаление товара
         *
         * @param {Object} model [Product model]
         * @param {Number} index [индекс в списке]
         * @return Promise
         */
        destroy: function(model, index = null) {
            if (!confirm(`Вы действительно хотите удалить: ${model.name}`)) {
                return false;
            }
            return deleteProduct(model, this.state).then((response) => {
                if (index !== null) {
                    this.collection.data.splice(index, 1);
                    this.collection.total -= 1;
                }
            });
        },

        /**
         * Изменение активности товара
         *
         * @param  {Object} model [Product model]
         * @param  {Boolean} value [value after changing]
         * @return {Promise}
         */
        onActiveChange: function(model, value) {
            let params = {id: model.id, active: value};
            return postProduct(params, this.state).then((response) => {
                // console.log(response);
            });
        },

        /**
         * Поиск товаров
         *
         * @param {String} value [поисковая строка]
         * @param {Function} [колбек для передачи результатов]
         * @return {void}
         */
        onSuggest: function(value, callback) {
            return getProductSuggest({q: value}).then((response) => {
                if (response.data && response.data.length) {
                    callback(response.data);
                }
            });
        },

        onSortChange: function({column, prop, order}) {
            console.log(column);
            console.log(prop);
            console.log(order);
        },
    },
});
