<?php

namespace Dashboard\Models;

use Illuminate\Database\Eloquent\Model;

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
            'title' => 'Input text',
            'icon' => 'el-icon-pencil6',
        ],
        [
            'name' => self::TEXTAREA,
            'title' => 'Textarea',
            'icon' => 'el-icon-typography',
        ],
        [
            'name' => self::INPUT_NUMBER,
            'title' => 'Input number',
            'icon' => 'el-icon-sort-numeric-asc',
        ],
        [
            'name' => self::WYSIWYG,
            'title' => 'WYSIWYG editor',
            'icon' => 'el-icon-insert-template',
        ],
        [
            'name' => self::TOGGLE,
            'title' => 'Toggle',
            'icon' => 'el-icon-checkbox-checked2',
        ],
        [
            'name' => self::RADIO_GROUP,
            'title' => 'Radio group',
            'icon' => 'el-icon-radio-checked',
        ],
        [
            'name' => self::MEDIA_FILE,
            'title' => 'File uploader',
            'icon' => 'el-icon-attachment',
        ],
        [
            'name' => self::DATEPICKER,
            'title' => 'Datepicker',
            'icon' => 'el-icon-calendar2',
        ],
        [
            'name' => self::DATETIMEPICKER,
            'title' => 'Datetimepicker',
            'icon' => 'el-icon-alarm',
        ],
        [
            'name' => self::SELECT,
            'title' => 'Select',
            'icon' => 'el-icon-list2',
        ],
        [
            'name' => self::SELECT_MULTIPLE,
            'title' => 'Select multiple',
            'icon' => 'el-icon-menu2',
        ],
        [
            'name' => self::ICONPICKER,
            'title' => 'Iconpicker',
            'icon' => 'el-icon-image2',
        ],
        [
            'name' => self::INPUT_PASSWORD,
            'title' => 'Password',
            'icon' => 'el-icon-lock',
        ],
    ];

    /**
     * Fetch all componenets
     *
     * @return array
     */
    public static function getComponents()
    {
        return self::components;
    }
}
