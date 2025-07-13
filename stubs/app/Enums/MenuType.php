<?php

namespace App\Enums;

enum MenuType: string
{
    case ARTICLE = 'article';
    case ROUTE = 'route';
    case URI = 'uri';

    public static function toArray(): array {
        return array_column(self::cases(), 'value');
    }
}
