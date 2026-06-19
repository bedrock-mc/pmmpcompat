<?php

declare(strict_types=1);

namespace pocketmine\utils;

abstract class Terminal
{
    public static string $FORMAT_BOLD = '';
    public static string $FORMAT_OBFUSCATED = '';
    public static string $FORMAT_ITALIC = '';
    public static string $FORMAT_UNDERLINE = '';
    public static string $FORMAT_STRIKETHROUGH = '';
    public static string $FORMAT_RESET = '';
    public static string $COLOR_BLACK = '';
    public static string $COLOR_DARK_BLUE = '';
    public static string $COLOR_DARK_GREEN = '';
    public static string $COLOR_DARK_AQUA = '';
    public static string $COLOR_DARK_RED = '';
    public static string $COLOR_PURPLE = '';
    public static string $COLOR_GOLD = '';
    public static string $COLOR_GRAY = '';
    public static string $COLOR_DARK_GRAY = '';
    public static string $COLOR_BLUE = '';
    public static string $COLOR_GREEN = '';
    public static string $COLOR_AQUA = '';
    public static string $COLOR_RED = '';
    public static string $COLOR_LIGHT_PURPLE = '';
    public static string $COLOR_YELLOW = '';
    public static string $COLOR_WHITE = '';
    public static string $COLOR_MINECOIN_GOLD = '';
    public static string $COLOR_MATERIAL_QUARTZ = '';
    public static string $COLOR_MATERIAL_IRON = '';
    public static string $COLOR_MATERIAL_NETHERITE = '';
    public static string $COLOR_MATERIAL_REDSTONE = '';
    public static string $COLOR_MATERIAL_COPPER = '';
    public static string $COLOR_MATERIAL_GOLD = '';
    public static string $COLOR_MATERIAL_EMERALD = '';
    public static string $COLOR_MATERIAL_DIAMOND = '';
    public static string $COLOR_MATERIAL_LAPIS = '';
    public static string $COLOR_MATERIAL_AMETHYST = '';
    public static string $COLOR_MATERIAL_RESIN = '';

    private static ?bool $formattingCodes = null;

    public static function hasFormattingCodes(): bool
    {
        if (self::$formattingCodes === null) {
            throw new \LogicException('Formatting codes have not been initialized');
        }
        return self::$formattingCodes;
    }

    public static function init(?bool $enableFormatting = null): void
    {
        self::$formattingCodes = $enableFormatting ?? false;
        if (!self::$formattingCodes) {
            self::$FORMAT_RESET = '';
            return;
        }
        self::$FORMAT_BOLD = "\x1b[1m";
        self::$FORMAT_OBFUSCATED = '';
        self::$FORMAT_ITALIC = "\x1b[3m";
        self::$FORMAT_UNDERLINE = "\x1b[4m";
        self::$FORMAT_STRIKETHROUGH = "\x1b[9m";
        self::$FORMAT_RESET = "\x1b[m";
        $color = static fn(int $code): string => "\x1b[38;5;" . $code . "m";
        self::$COLOR_BLACK = $color(16);
        self::$COLOR_DARK_BLUE = $color(19);
        self::$COLOR_DARK_GREEN = $color(34);
        self::$COLOR_DARK_AQUA = $color(37);
        self::$COLOR_DARK_RED = $color(124);
        self::$COLOR_PURPLE = $color(127);
        self::$COLOR_GOLD = $color(214);
        self::$COLOR_GRAY = $color(145);
        self::$COLOR_DARK_GRAY = $color(59);
        self::$COLOR_BLUE = $color(63);
        self::$COLOR_GREEN = $color(83);
        self::$COLOR_AQUA = $color(87);
        self::$COLOR_RED = $color(203);
        self::$COLOR_LIGHT_PURPLE = $color(207);
        self::$COLOR_YELLOW = $color(227);
        self::$COLOR_WHITE = $color(231);
    }

    public static function isInit(): bool
    {
        return self::$formattingCodes !== null;
    }

    public static function toANSI(string $string): string
    {
        if (!self::isInit()) {
            self::init(false);
        }
        $out = '';
        foreach (TextFormat::tokenize($string) as $token) {
            $out .= match ($token) {
                TextFormat::BOLD => self::$FORMAT_BOLD,
                TextFormat::ITALIC => self::$FORMAT_ITALIC,
                TextFormat::RESET => self::$FORMAT_RESET,
                TextFormat::BLACK => self::$COLOR_BLACK,
                TextFormat::DARK_BLUE => self::$COLOR_DARK_BLUE,
                TextFormat::DARK_GREEN => self::$COLOR_DARK_GREEN,
                TextFormat::DARK_AQUA => self::$COLOR_DARK_AQUA,
                TextFormat::DARK_RED => self::$COLOR_DARK_RED,
                TextFormat::DARK_PURPLE => self::$COLOR_PURPLE,
                TextFormat::GOLD => self::$COLOR_GOLD,
                TextFormat::GRAY => self::$COLOR_GRAY,
                TextFormat::DARK_GRAY => self::$COLOR_DARK_GRAY,
                TextFormat::BLUE => self::$COLOR_BLUE,
                TextFormat::GREEN => self::$COLOR_GREEN,
                TextFormat::AQUA => self::$COLOR_AQUA,
                TextFormat::RED => self::$COLOR_RED,
                TextFormat::LIGHT_PURPLE => self::$COLOR_LIGHT_PURPLE,
                TextFormat::YELLOW => self::$COLOR_YELLOW,
                TextFormat::WHITE => self::$COLOR_WHITE,
                default => $token,
            };
        }
        return $out;
    }

    public static function write(string $line): void
    {
        echo self::toANSI($line);
    }

    public static function writeLine(string $line): void
    {
        echo self::toANSI($line) . self::$FORMAT_RESET . PHP_EOL;
    }
}
