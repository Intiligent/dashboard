import Vue from 'vue';
import VueRouter from 'vue-router';
import {
    Button,
    Input,
    Dropdown,
    DropdownMenu,
    DropdownItem,
    Menu,
    Submenu,
    MenuItem,
    MenuItemGroup,
} from 'element-ui';

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
});

export default {

    mounted() {
        //
    },

    created() {
        this.route = window.route;
    },

    data() {
        return {
            // состояние приложения
            state: {
                isLoading: false,
                isNavbarCollapse: false,
            },
            q: this.$router.currentRoute.query.q,
        }
    },

    components: {
        ElButton: Button,
        ElInput: Input,
        ElMenu: Menu,
        ElDropdown: Dropdown,
        ElDropdownMenu: DropdownMenu,
        ElDropdownItem: DropdownItem,
        ElSubmenu: Submenu,
        ElMenuItem: MenuItem,
        ElMenuItemGroup: MenuItemGroup,
    },

    methods: {
        onNavbarToggle: function() {
            this.state.isNavbarCollapse = !this.state.isNavbarCollapse;
        },
    },

    computed: {
        //
    },

    router: router,
}
