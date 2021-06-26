<?php

namespace Dashboard\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Str;

class Component extends Model
{
    const INPUT_TEXT = 'input-text';
    const INPUT_NUMBER = 'input-number';
    const INPUT_PASSWORD = 'input-password';
    const DATEPICKER = 'datepicker';
    const DATETIMEPICKER = 'datetimepicker';
    const TEXTAREA = 'textarea';
    const WYSIWYG = 'wysiwyg';
    const TOGGLE = 'toggle';
    const RADIO_GROUP = 'radio-group';
    const MEDIA_FILE = 'media-file';
    const SELECT = 'select';
    const SELECT_MULTIPLE = 'select-multiple';
    const ICONPICKER = 'iconpicker';

    const components = [
        [
            'name' => self::INPUT_TEXT,
            'title' => 'Поле ввода',
            'icon' => 'el-icon-pencil6',
        ],
        [
            'name' => self::TEXTAREA,
            'title' => 'Текстовое поле',
            'icon' => 'el-icon-typography',
        ],
        [
            'name' => self::INPUT_NUMBER,
            'title' => 'Числовое поле',
            'icon' => 'el-icon-sort-numeric-asc',
        ],
        [
            'name' => self::WYSIWYG,
            'title' => 'Визуальный редактор',
            'icon' => 'el-icon-insert-template',
        ],
        [
            'name' => self::TOGGLE,
            'title' => 'Переключатель',
            'icon' => 'el-icon-checkbox-checked2',
        ],
        [
            'name' => self::RADIO_GROUP,
            'title' => 'Групповой выбор',
            'icon' => 'el-icon-radio-checked',
        ],
        [
            'name' => self::MEDIA_FILE,
            'title' => 'Выбор файла',
            'icon' => 'el-icon-attachment',
        ],
        [
            'name' => self::DATEPICKER,
            'title' => 'Выбор даты',
            'icon' => 'el-icon-calendar2',
        ],
        [
            'name' => self::DATETIMEPICKER,
            'title' => 'Выбор даты и время',
            'icon' => 'el-icon-alarm',
        ],
        [
            'name' => self::SELECT,
            'title' => 'Список',
            'icon' => 'el-icon-list2',
        ],
        [
            'name' => self::SELECT_MULTIPLE,
            'title' => 'Множественный список',
            'icon' => 'el-icon-menu2',
        ],
        [
            'name' => self::ICONPICKER,
            'title' => 'Выбор иконки',
            'icon' => 'el-icon-image2',
        ],
        [
            'name' => self::INPUT_PASSWORD,
            'title' => 'Пароль',
            'icon' => 'el-icon-lock',
        ],
    ];

    /**
     * Получить все компоненты
     *
     * @return array
     */
    public static function getComponents()
    {
        return self::components;
    }
}
