<?php

namespace Dashboard\Database\Seeders;

use Illuminate\Database\Seeder;
use Dashboard\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $collection = [
            [
                'title' => 'Meta info',
                'description' => 'Project name, title, keywords, description',
                'key' => 'META',
                'icon' => 'el-icon-newspaper',
                'is_system' => true,
                'children' => [
                    [
                        'key' => 'PROJECT_NAME',
                        'value' => 'Dashboard',
                        'title' => 'Project name',
                        'description' => 'Site name (will be used when sending the email as the senders name)',
                        'icon' => 'el-icon-paragraph-left3',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'META_TITLE',
                        'value' => 'Manage your data easy',
                        'title' => 'Meta title',
                        'description' => 'The title of the page in your browser that appears in search engine results',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'META_DESCRIPTION',
                        'value' => '',
                        'title' => 'Meta description',
                        'description' => 'Designed to create a brief description of the page. Meta tag description can affect the position of the site in the output of those search engines that take it into account',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'META_KEYWORDS',
                        'value' => '',
                        'title' => 'Meta keywords',
                        'description' => 'Designed to create a brief description of keywords on the page. Keywords are separated by commas. Does not affect the position of the site in the output.',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'title' => 'General settings',
                'description' => 'Email, caching',
                'key' => 'GENERAL',
                'icon' => 'el-icon-equalizer2',
                'is_system' => true,
                'children' => [
                    [
                        'key' => 'LOGO_BASE',
                        'value' => '/storage/settings/logo-base.png',
                        'title' => 'Logo',
                        'description' => 'Logo in original format',
                        'type' => 'file',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'LOGO_SQUARE',
                        'value' => '/storage/settings/logo-square.png',
                        'title' => 'Logo square',
                        'description' => 'Logo with aspect ratio (1:1)',
                        'type' => 'file',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'FAVICON',
                        'value' => '',
                        'title' => 'Favicon',
                        'description' => 'A website icon in (.ico) format. Displayed by the browser in the tab before the page title',
                        'type' => 'file',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'LAYOUT_BOX_SIZE',
                        'value' => 'fix',
                        'title' => 'Content block view',
                        'description' => 'How to display content block for admin panel. (fixed size, full width)',
                        'type' => 'radio-group',
                        'properties' => [
                            'data' => [
                                ['value' => 'fix', 'label' => 'Fixed size'],
                                ['value' => 'wide', 'label' => 'Wide'],
                                ['value' => 'full', 'label' => 'Full width'],
                            ],
                        ],
                        'is_system' => true,
                    ],
                    [
                        'key' => 'VIEWPORT',
                        'value' => 'initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width',
                        'title' => 'Displaying the screen width',
                        'description' => 'Allows you to control the size of the viewing window and page scale',
                        'type' => 'radio-group',
                        'properties' => [
                            'data' => [
                                ['value' => '-', 'label' => 'Origin view'],
                                ['value' => 'initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width', 'label' => 'Responsible view'],
                            ],
                        ],
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'title' => 'Analytics',
                'description' => 'Analytics: traffic counters',
                'key' => 'ANALYTICS',
                'icon' => 'el-icon-stats-bars2',
                'is_system' => true,
                'children' => [
                    [
                        'key' => 'GOOGLE_ANALYTICS_CODE',
                        'value' => '',
                        'title' => 'Google analytics code',
                        'description' => 'Google analytics allows you to track user interactions with your website. Insert code with tag <script>',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'YANDEX_ANALYTICS_CODE',
                        'value' => '',
                        'title' => 'Yandex analytics code',
                        'description' => 'Yandex analytics allows you to track user interactions with your site. Insert code with tag <script>',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'FACEBOOK_PIXEL_CODE',
                        'value' => '',
                        'title' => 'Pixel Facebook',
                        'description' => 'The Facebook Pixel is a code snippet for a website that allows you to measure performance, optimize ads, and create audiences for ad campaigns. Insert code with tag <script>',
                        'type' => 'textarea',
                        'is_system' => true,
                    ],
                ],
            ],
            [
                'title' => 'Contacts',
                'description' => 'Social networks, addresses, phone numbers',
                'key' => 'CONTACTS',
                'icon' => 'el-icon-notebook',
                'children' => [
                    [
                        'key' => 'CONTACT_PHONE',
                        'value' => '',
                        'title' => 'Phone',
                        'icon' => 'el-icon-iphone',
                    ],
                    [
                        'key' => 'CONTACT_EMAIL',
                        'value' => '',
                        'title' => 'Email',
                        'icon' => 'el-icon-envelop2',
                    ],
                    [
                        'key' => 'INSTAGRAM',
                        'value' => '',
                        'title' => 'Instagram',
                        'icon' => 'el-icon-instagram',
                    ],
                    [
                        'key' => 'FACEBOOK',
                        'value' => '',
                        'title' => 'Facebook',
                        'icon' => 'el-icon-facebook2',
                    ],
                ],
            ],
            [
                'title' => 'Media library',
                'description' => 'Media library display and appearance settings',
                'key' => 'MEDIA',
                'icon' => 'el-icon-image4',
                'is_system' => true,
                'children' => [
                    [
                        'key' => 'GALLERY_ITEMS_ON_PAGE',
                        'value' => 60,
                        'title' => 'Items on page',
                        'type' => 'input-number',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'GALLERY_FILE_SIZE_LIMIT',
                        'value' => 10,
                        'title' => 'Maximum size of the uploaded file (Mb)',
                        'type' => 'input-number',
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'title' => 'Notification',
                'description' => 'Notifications, messages, email subjects',
                'key' => 'NOTIFICATION',
                'icon' => 'el-icon-bell2'
            ],
            [
                'title' => 'Maintenance',
                'description' => 'Site status, text when site is disabled, IP addresses of exceptions',
                'key' => 'SERVICE',
                'icon' => 'el-icon-wrench',
                'is_system' => true,
                'children' => [
                    [
                        'key' => 'SERVICE_ENABLE',
                        'value' => true,
                        'title' => 'Site available',
                        'description' => 'Turning the site on/off. It is recommended to disable the site when performing technical works',
                        'type' => 'switch',
                        'is_system' => true,
                    ],
                ]
            ],
        ];

        $this->makeSettings($collection);
    }

    /**
     * Create settings tree
     *
     * @param array $tree
     * @return void
     */
    private function makeSettings($collection)
    {
        foreach ($collection as $root) {
            Setting::create($root);
        }
    }
}
