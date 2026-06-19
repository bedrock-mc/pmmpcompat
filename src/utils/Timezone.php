<?php

declare(strict_types=1);

namespace pocketmine\utils;

class Timezone
{
    public static function get(): string
    {
        return ini_get('date.timezone') ?: date_default_timezone_get();
    }

    public static function init(): void
    {
        $timezone = self::get();
        if ($timezone === '' || @date_default_timezone_set($timezone) === false) {
            $timezone = self::detectSystemTimezone() ?: 'UTC';
            date_default_timezone_set($timezone);
            ini_set('date.timezone', $timezone);
        }
    }

    public static function detectSystemTimezone(): string|false
    {
        $timezone = date_default_timezone_get();
        return $timezone !== '' ? $timezone : false;
    }
}
