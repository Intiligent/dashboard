<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Dashboard\Models\SettingGroup;
use Dashboard\Models\Setting;
use Dashboard\Models\Component;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $collection = [
            [
                'name' => 'Мета информация',
                'description' => 'Имя проекта, заголовок, ключевые слова, описание',
                'code' => 'meta',
                'icon' => 'el-icon-newspaper',
                'items' => [
                    [
                        'key' => 'app_name',
                        'value' => 'Vintage',
                        'name' => 'Имя проекта',
                        'description' => 'Название сайта (будет использоваться при отправке письма, как имя отправителя)',
                        'icon' => 'el-icon-paragraph-left3',
                        'is_system' => true,
                    ],
                    [
                        'key' => 'meta_title',
                        'value' => 'Project of your dream',
                        'name' => 'Мета заголовок',
                        'description' => 'Заголовок страницы в браузере, который отображается в выдаче поисковых систем',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'meta_description',
                        'value' => '',
                        'name' => 'Мета описание',
                        'description' => 'Предназначен для создания краткого описания страницы. Мета тег description может влиять на позиции сайта в выдаче тех поисковых систем, которые его учитывают',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'meta_keywords',
                        'value' => '',
                        'name' => 'Мета ключевые слова',
                        'description' => 'Предназначен для создания краткого описания ключевых слов на странице. Ключевые слова разделяются запятыми. Не влияет на позиции сайта в выдаче.',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'name' => 'Общие настройки',
                'description' => 'Email, кеширование',
                'code' => 'base',
                'icon' => 'el-icon-equalizer2',
                'items' => [
                    [
                        'key' => 'LOGO_BASE',
                        'value' => '',
                        'name' => 'Логотип',
                        'description' => 'Логотип в оригинальном формате',
                        'type' => Component::MEDIA_FILE,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'LOGO_SQUARE',
                        'value' => '',
                        'name' => 'Логотип квадратный',
                        'description' => 'Логотип со сторонами (1:1)',
                        'type' => Component::MEDIA_FILE,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'FAVICON',
                        'value' => '',
                        'name' => 'Favicon',
                        'description' => 'Значок веб-сайта в формате (.ico). Отображается браузером во вкладке перед названием страницы',
                        'type' => Component::MEDIA_FILE,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'LAYOUT_BOX_SIZE',
                        'value' => 'fix',
                        'name' => 'Вид блока контента',
                        'description' => 'Как отображать блок с контентом для админ панели. (фиксированный размер, на всю ширину)',
                        'type' => Component::RADIO_GROUP,
                        'data' => [
                            ['value' => 'fix', 'label' => 'Фиксированный размер'],
                            ['value' => 'wide', 'label' => 'Широкий'],
                            ['value' => 'full', 'label' => 'На всю ширину'],
                        ],
                        'is_system' => true,
                    ],
                    [
                        'key' => 'VIEWPORT',
                        'value' => 'initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width',
                        'name' => 'Отображение ширины экрана',
                        'description' => 'Позволяет контролировать размер окна просмотра и масштаб страницы',
                        'type' => Component::RADIO_GROUP,
                        'data' => [
                            ['value' => '-', 'label' => 'Оригинальный вид'],
                            ['value' => 'initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width', 'label' => 'Адаптивный вид'],
                        ],
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'name' => 'Аналитика',
                'description' => 'Аналитика: счетчики посещаемости',
                'code' => 'analytics',
                'icon' => 'el-icon-stats-bars2',
                'items' => [
                    [
                        'key' => 'GOOGLE_ANALYTICS_CODE',
                        'value' => '',
                        'name' => 'Google analytics code',
                        'description' => 'Google analytics позволяет отслеживать взаимодействия пользователей с вашим сайтом. Вставить код с тегом <script>',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'YANDEX_ANALYTICS_CODE',
                        'value' => '',
                        'name' => 'Yandex analytics code',
                        'description' => 'Yandex analytics позволяет отслеживать взаимодействия пользователей с вашим сайтом. Вставить код с тегом <script>',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'FACEBOOK_PIXEL_CODE',
                        'value' => '',
                        'name' => 'Пиксель Facebook',
                        'description' => 'Пиксель Facebook — это фрагмент кода для сайта, который позволяет измерять результативность, оптимизировать рекламу и создавать аудитории для рекламных кампаний. Вставить код с тегом <script>',
                        'type' => Component::TEXTAREA,
                        'is_system' => true,
                    ],
                ],
            ],
            [
                'name' => 'Контактные данные',
                'description' => 'Социальные сети, адреса, телефоны',
                'code' => 'contacts',
                'icon' => 'el-icon-notebook',
                'items' => [
                    [
                        'key' => 'vk',
                        'value' => '',
                        'name' => 'Вконтакте',
                        'icon' => 'el-icon-sphere',
                    ],
                    [
                        'key' => 'ODNOKLASSNIKI',
                        'value' => '',
                        'name' => 'Однокласники',
                        'icon' => 'el-icon-sphere',
                    ],
                    [
                        'key' => 'contact_phone',
                        'value' => '',
                        'name' => 'Телефон',
                        'icon' => 'el-icon-iphone',
                    ],
                    [
                        'key' => 'contact_email',
                        'value' => '',
                        'name' => 'Email',
                        'icon' => 'el-icon-envelop2',
                    ],
                    [
                        'key' => 'instagram',
                        'value' => '',
                        'name' => 'Instagram',
                        'icon' => 'el-icon-instagram',
                    ],
                    [
                        'key' => 'facebook',
                        'value' => '',
                        'name' => 'Facebook',
                        'icon' => 'el-icon-facebook2',
                    ],
                ],
            ],
            [
                'name' => 'Медиа библиотека',
                'description' => 'Настройки отображения и внешнего вида медиа библиотеки',
                'code' => 'media',
                'icon' => 'el-icon-image4',
                'items' => [
                    [
                        'key' => 'GALLERY_ITEMS_ON_PAGE',
                        'value' => 60,
                        'name' => 'Количество обьектов на странице',
                        'type' => Component::INPUT_NUMBER,
                        'is_system' => true,
                    ],
                    [
                        'key' => 'GALLERY_FILE_SIZE_LIMIT',
                        'value' => 10,
                        'name' => 'Максимальный размер загружаемого файла (Мб)',
                        'type' => Component::INPUT_NUMBER,
                        'is_system' => true,
                    ],
                ]
            ],
            [
                'name' => 'Уведомления',
                'description' => 'Уведомления, сообщения, темы для писем',
                'code' => 'notify',
                'icon' => 'el-icon-bell2'
            ],
            [
                'name' => 'Обслуживание',
                'description' => 'Состояние сайта, текст при отключенном сайте, IP адреса исключений',
                'code' => 'service',
                'icon' => 'el-icon-wrench',
                'items' => [
                    [
                        'key' => 'SERVICE_ENABLE',
                        'value' => true,
                        'name' => 'Состояние сайта (ВКЛ/ВЫКЛ)',
                        'description' => 'Включение/выключение сайта. Рекомендуется отключать сайт при проведении технических работ',
                        'type' => Component::TOGGLE,
                        'is_system' => true,
                    ],
                ]
            ],
        ];

        foreach($collection as $groupItem) {
            $group = SettingGroup::firstOrNew(Arr::only($groupItem, ['name']), Arr::except($groupItem, ['items']));
            $group->save();
            if (Arr::has($groupItem, ['items'])) {
                foreach ($groupItem['items'] as $setting) {
                    $setting['group_id'] = $group->id;
                    Setting::firstOrNew(Arr::only($setting, ['key', 'value']), $setting)->save();
                }
            }
        }
    }
}
