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
                'name' => 'Main menu',
                'code' => 'main',
                'children' => [
                    [
                        'name' => 'Service',
                        'value' => '/service',
                        'type' => Menu::TYPE_URI,
                    ],
                    [
                        'name' => 'About us',
                        'value' => '/about',
                        'type' => Menu::TYPE_URI,
                    ],
                    [
                        'name' => 'Contacts',
                        'value' => '/contacts',
                        'type' => Menu::TYPE_URI,
                    ],
                ]
            ],
            [
                'name' => 'Footer menu',
                'code' => 'bottom',
            ],
            [
                'name' => 'Dashboard',
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
                                'name' => 'Article pages',
                                'value' => 'dashboard.articleList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-bookmark',
                                'is_system' => true,
                            ],
                            // [
                            //     'name' => 'Banners',
                            //     'value' => '/dashboard/banner',
                            //     'type' => Menu::TYPE_URI,
                            //     'icon' => 'el-icon-image-compare',
                            // ],
                        ]
                    ],
                    [
                        'name' => 'System',
                        'value' => '#',
                        'type' => 'uri',
                        'is_system' => true,
                        'children' => [
                            [
                                'name' => 'Menu',
                                'value' => 'dashboard.menu',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-list-unordered',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Users',
                                'value' => 'dashboard.userList',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-users2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Access controll',
                                'value' => '/acl',
                                'type' => 'uri',
                                'icon' => 'el-icon-user-lock',
                                'is_system' => true,
                                'children' => [
                                    [
                                        'name' => 'Roles',
                                        'value' => 'dashboard.roles',
                                        'type' => Menu::TYPE_ROUTE,
                                        'icon' => 'el-icon-user-lock',
                                        'is_system' => true,
                                    ],
                                    [
                                        'name' => 'Persmissions',
                                        'value' => 'dashboard.permissions',
                                        'type' => Menu::TYPE_ROUTE,
                                        'icon' => 'el-icon-lock',
                                        'is_system' => true,
                                    ],
                                ]
                            ],
                            [
                                'name' => 'Setting',
                                'value' => 'dashboard.settings',
                                'type' => Menu::TYPE_ROUTE,
                                'icon' => 'el-icon-equalizer2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Reports',
                                'value' => '#',
                                'type' => Menu::TYPE_URI,
                                'icon' => 'el-icon-chart',
                                'active' => false,
                                'children' => [
                                    [
                                        'name' => 'Activity',
                                        'value' => '/log/action',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-history',
                                        'active' => false,
                                    ],
                                    [
                                        'name' => 'Send mail',
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
        ];

        $this->makeMenu($navigations);
    }

    /**
     * Create tree menu
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
