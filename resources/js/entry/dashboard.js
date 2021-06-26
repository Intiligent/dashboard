import Vue from 'vue';
import {
    ButtonGroup,
    Breadcrumb,
    BreadcrumbItem,
    Table,
    TableColumn,
    Tooltip,
    Avatar,
} from 'element-ui';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElButtonGroup: ButtonGroup,
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElTooltip: Tooltip,
        ElAvatar: Avatar,
    },

    data: function() {
        return {
            //
        }
    },

    methods: {
        /**
         * отправка формы
         *
         * @param event - событие нажатия кнопки
         * @return Promise
         */
        // submit: function(event) {
        //     return postProduct(this.product, this.state).then((response) => {
        //         if (response.error === 201) {
        //             window.location = route('dashboard.productList');
        //         }
        //     });
        // },

        /**
         * Изменение активности товара
         *
         * @param  {Object} model [Product model]
         * @param  {Boolean} value [value after changing]
         * @return {Promise}
         */
        // onActiveChange: function(model, value) {
        //     let params = {id: model.id, active: value};
        //     return postProduct(params, this.state).then((response) => {
        //         // console.log(response);
        //     });
        // },
    },
});
