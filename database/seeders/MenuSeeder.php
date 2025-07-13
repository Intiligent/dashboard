<?php

namespace Dashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\MenuType;
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
                        'name' => 'Home',
                        'value' => '/',
                        'type' => MenuType::URI,
                    ],
                    [
                        'name' => 'About',
                        'value' => '/about',
                        'type' => MenuType::URI,
                    ],
                ]
            ],
            [
                'name' => 'Bottom menu',
                'code' => 'bottom',
            ],
            [
                'name' => 'Dashboard',
                'code' => 'dashboard',
                'children' => [
                    [
                        'name' => 'Dashboard',
                        'value' => 'dashboard.home',
                        'type' => MenuType::ROUTE,
                        'icon' => 'el-icon-meter2',
                        'is_system' => true,
                    ],
                    [
                        'name' => 'Content',
                        'value' => '#',
                        'type' => MenuType::URI,
                        'is_system' => true,
                        'children' => [
                            [
                                'name' => 'Pages',
                                'value' => 'dashboard.articleList',
                                'type' => MenuType::ROUTE,
                                'icon' => 'el-icon-bookmark',
                                'is_system' => true,
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
                                'name' => 'Navigation',
                                'value' => 'dashboard.menu',
                                'type' => MenuType::ROUTE,
                                'icon' => 'el-icon-list-unordered',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Users',
                                'value' => 'dashboard.userList',
                                'type' => MenuType::ROUTE,
                                'icon' => 'el-icon-users2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Access Control',
                                'value' => '/acl',
                                'type' => 'uri',
                                'icon' => 'el-icon-user-lock',
                                'is_system' => true,
                                'children' => [
                                    [
                                        'name' => 'Roles',
                                        'value' => 'dashboard.roles',
                                        'type' => MenuType::ROUTE,
                                        'icon' => 'el-icon-user-lock',
                                        'is_system' => true,
                                    ],
                                    [
                                        'name' => 'Permissions',
                                        'value' => 'dashboard.permissions',
                                        'type' => MenuType::ROUTE,
                                        'icon' => 'el-icon-lock',
                                        'is_system' => true,
                                    ],
                                ]
                            ],
                            [
                                'name' => 'Settings',
                                'value' => 'dashboard.settings',
                                'type' => MenuType::ROUTE,
                                'icon' => 'el-icon-equalizer2',
                                'is_system' => true,
                            ],
                            [
                                'name' => 'Reports',
                                'value' => '#',
                                'type' => MenuType::URI,
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
                                        'name' => 'Outbox',
                                        'value' => '/log/mail',
                                        'type' => 'uri',
                                        'icon' => 'el-icon-envelop2',
                                        'active' => false,
                                    ],
                                    [
                                        'name' => 'API Requests Log',
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
     * Make tree navigation
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
