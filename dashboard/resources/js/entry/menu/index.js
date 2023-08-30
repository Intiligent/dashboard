import { createApp, h, ref } from 'vue';
import { createRouter, createWebHistory, useRoute, useRouter } from 'vue-router';
import {
    ElAvatar,
    ElButton,
    ElDropdown,
    ElDropdownItem,
    ElDropdownMenu,
    ElEmpty,
    ElIcon,
    ElInput,
    ElForm,
    ElFormItem,
    ElLink,
    ElMenu,
    ElMenuItem,
    ElMenuItemGroup,
    ElMessageBox,
    ElSubMenu,
    ElSwitch,
    ElTabs,
    ElTabPane,
    ElTooltip,
    ElTree,
} from 'element-plus';
import {
    Menu as IconMenu,
    Setting as IconSetting,
    Search as IconSearch,
} from '@element-plus/icons-vue';
import ModalMenuItem from '@dashboard/components/modal/modal.menu-item.vue';
import { postMenu, deleteMenu } from '@dashboard/service/request/menu';

const router = createRouter({
    history: createWebHistory(),
    routes: [],
});

const app = createApp({
    components: {
        ElAvatar,
        ElButton,
        ElDropdown,
        ElDropdownItem,
        ElDropdownMenu,
        ElEmpty,
        ElIcon,
        ElInput,
        ElForm,
        ElFormItem,
        ElLink,
        ElMenu,
        ElMenuItem,
        ElMenuItemGroup,
        ElMessageBox,
        ElSubMenu,
        ElSwitch,
        ElTabs,
        ElTabPane,
        ElTooltip,
        ElTree,

        IconMenu,
        IconSearch,
        IconSetting,
    },

    watch: {
        currentMenuGroup: function(value) {
            this.router.push({ path: location.pathname, hash: '#' + value });
        },
        searchQuery: function(value) {
            for (const tree of this.treeRef) {
                tree.filter(value);
            }
        },
    },

    setup() {
        const searchQuery = ref(null);
        const state = ref({
            isLoading: false,
            isMa: false,
        });
        const route = useRoute();
        const router = useRouter();
        const routes = window.app.routes;
        const tree = ref(window.app.tree);
        const treeRef = ref(null);
        const currentTabName = route.hash ? route.hash.replace(/^#/, '') : window.app.tree[0].code;
        const currentMenuGroup = ref(currentTabName);
        const isItemLoading = ref({});

        const handleMenuCommand = (command) => {
            if (command === 'add') {
                const parent = tree.value.find((item) => {
                    return item.code === currentMenuGroup.value;
                });
                const model = { type: 'uri', parent_id: parent.id };
                onMenuItemModify(model, parent);
            }
        };

        const allowTreeDrop = (draggingNode, dropNode, type) => {
            if (dropNode.data.label === 'Level two 3-1') {
                return type !== 'inner';
            }
            return true;
        };

        const allowTreeDrag = (draggingNode) => {
            return !draggingNode.data.label.includes('Level three 3-1-1');
        };

        const onFilterNode = (value, data) => {
            if (!value) {
                return true;
            }
            return data.name.toLowerCase().includes(value.toLowerCase());
        };

        const removeMenuItem = async (node, data) => {
            console.log('remove', node, data);

            if (!confirm('Are you sure you want delete menu item?')) {
                return;
            }
            const payload = { id: data.id };
            const response = await deleteMenu(payload);
            console.log(response);
            if (response.error === 200) {
                const parent = node.parent;
                const children = parent.data.children || parent.data;
                const index = children.findIndex((item) => item.id === data.id);
                children.splice(index, 1);
                tree.value = [...tree.value];
            }
        };

        /**
         * Add or update menu item
         *
         * @param {Object} model [ModelMenu]
         * @param {Node} parent [parent node]
         * @return {Promise}
         */
        const onMenuItemModify = (model, parent) => {
            return ElMessageBox({
                title: 'Menu item',
                message: h(ModalMenuItem, {
                    model,
                    routes,
                    tree: tree.value,
                    onCreate: function(data) {
                        if (!parent.children) {
                            parent.children = [];
                        }
                        parent.children.push(data);
                    },
                }),
                showConfirmButton: false,
                customClass: 'el-message--sm',
            }).catch((error) => {
                console.log(error);
            });
        };

        const onChildMenuItemAdd = (parent) => {
            console.log('parent', parent);

            const model = {
                type: 'uri',
                parent_id: parent.id,
            };
            onMenuItemModify(model, parent);
        };

        const onMenuItemActiveChange = async (model) => {
            const payload = { id: model.id, active: model.active };
            isItemLoading.value[model.id] = true;
            try {
                const response = await postMenu(payload);
                if (response.error === 200) {
                    return true;
                }
            } finally {
                delete isItemLoading.value[model.id];
            }
        };

        const onMenuItemDelete = (model) => {
            console.log('model', model);
        }

        return {
            allowTreeDrop,
            allowTreeDrag,
            currentMenuGroup,
            handleMenuCommand,
            isItemLoading,
            onFilterNode,
            onMenuItemDelete,
            onMenuItemModify,
            onMenuItemActiveChange,
            onChildMenuItemAdd,
            router,
            searchQuery,
            state,
            tree,
            treeRef,
            removeMenuItem,
        }
    },
}).use(router);

router.isReady().then(() => {
    app.mount('#root');
});
