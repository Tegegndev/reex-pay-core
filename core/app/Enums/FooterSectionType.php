<?php

namespace App\Enums;

enum FooterSectionType: string
{
    case TEXT   = 'text';
    case LINK   = 'link';
    case PAGE   = 'page';
    case SOCIAL = 'social';

    public static function options(): array
    {
        return [
            self::TEXT,
            self::PAGE,
            self::LINK,
            self::SOCIAL,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::TEXT   => __('Text'),
            self::PAGE   => __('Page'),
            self::LINK   => __('Link'),
            self::SOCIAL => __('Social'),
        };
    }

    public function class(): string
    {
        return match ($this) {
            self::TEXT => 'col-md-4 col-sm-6 col-xs-6',
            self::PAGE => 'col-md-2 col-sm-6 col-xs-6',
            self::LINK, self::SOCIAL => 'col-md-3 col-sm-6 col-xs-6',
        };
    }
}
