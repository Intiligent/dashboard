<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $navigations = [
            [
                'name' => 'Основное меню',
                'code' => 'main',
                'children' => [
                    [
                        'name' => 'Каталог продукции',
                        'value' => 'face.catalog',
                        'type' => Menu::TYPE_ROUTE,
                    ],
                    [
                        'name' => 'Услуги',
                        'value' => '/service',
                        'type' => Menu::TYPE_URI,
                    ],
                    [
                        'name' => 'О нас',
                        'value' => '/about',
                        'type' => Menu::TYPE_URI,
                    ],
                    [
                        'name' => 'Оплата и доставка',
                        'value' => '/delivery-payment',
                        'type' => Menu::TYPE_URI,
                    ],
                    [
                        'name' => 'Контакты',
                        'value' => '/contact',
                        'type' => Menu::TYPE_URI,
                    ],
                ]
            ],
            [
                'name' => 'Нижнее меню',
                'code' => 'bottom',
            ],
            [
                'name' => 'Админ панель',
                'code' => 'dashboard',
                'children' => [
                    [
                        'name' => 'Dashboard',
                        'value' => 'dashboard.home',
                        'type' => Menu::TYPE_ROUTE,
                        'icon' => 'el-icon-meter2',
                        'is_system' => true,
                    ],
                    [
                        'name' => 'Content',
                        'value' => '#',
                        'type' => Menu::TYPE_URI,
                        'is_system' => true,
                        'children' => [
                            [
                                'name' => 'Заказы',
                                'value' => 'dashboard.orderList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-bag',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Каталог товаров',
                                'value' => 'dashboard.catalog',
                                'type' => 'uri',
                                'icon' => 'el-icon-folder-open2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Все товары',
                                'value' => 'dashboard.productList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-clipboard3',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Текстовые страницы',
                                'value' => 'dashboard.articleList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-bookmark',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Баннеры',
                                'value' => '/banner',
                                'type' => Menu::TYPE_URI,
                                'icon' => 'el-icon-image-compare',
                            ],
                        ]
                    ],
                    [
                        'name' => 'System',
                        'value' => '#',
                        'type' => 'uri',
                        'is_system' => true,
                        'children' => [
                            [
                                'name' => 'Меню',
                                'value' => 'dashboard.menu',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-list-unordered',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Пользователи',
                                'value' => 'dashboard.userList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-users2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Управление доступом',
                                'value' => '/acl',
                                'type' => 'uri',
                                'icon' => 'el-icon-user-lock',
                                'is_system' => true,
                                'children' => [
                                    [
                                        'name' => 'Роли пользователей',
                                        'value' => '/acl/role',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-user-lock',
                                        'is_system' => true,
                                    ],
                                    [
                                        'name' => 'Права доступа',
                                        'value' => '/acl/permission',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-lock',
                                        'is_system' => true,
                                    ],
                                    [
                                        'name' => 'Группы доступов',
                                        'value' => '/acl/group',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-price-tags2',
                                        'is_system' => true,
                                    ],
                                ]
                            ],
                            [
                                'name' => 'Настройки',
                                'value' => 'dashboard.settings',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-equalizer2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Отчеты',
                                'value' => '#',
                                'type' => Menu::TYPE_URI,
                                'icon' => 'el-icon-chart',
                                'active' => false,
                                'children' => [
                                    [
                                        'name' => 'История активности',
                                        'value' => '/log/action',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-history',
                                        'active' => false,
                                    ],
                                    [
                                        'name' => 'Отправленные письма',
                                        'value' => '/log/mail',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-envelop2',
                                        'active' => false,
                                    ],
                                    [
                                        'name' => 'API logs',
                                        'value' => '/api-logs',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-file-xml2',
                                        'active' => false,
                                    ],
                                ]
                            ],
                        ]
                    ],
                ]
            ],
            // [
            //     'name' => 'Админ панель на главной',
            //     'code' => 'dashboard-home'
            // ],
        ];

        $this->makeMenu($navigations);
    }

    /**
     * Создание древовидного меню из дерева
     *
     * @param array $tree
     * @return void
     */
    private function makeMenu($navigations)
    {
        foreach ($navigations as $root) {
            Menu::create($root);
        }
    }
}
