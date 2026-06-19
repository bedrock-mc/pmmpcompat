<?php

declare(strict_types=1);

namespace pocketmine\utils;

abstract class TextFormat
{
    public const ESCAPE = "\xc2\xa7";
    public const EOL = "\n";

    public const BLACK = self::ESCAPE . '0';
    public const DARK_BLUE = self::ESCAPE . '1';
    public const DARK_GREEN = self::ESCAPE . '2';
    public const DARK_AQUA = self::ESCAPE . '3';
    public const DARK_RED = self::ESCAPE . '4';
    public const DARK_PURPLE = self::ESCAPE . '5';
    public const GOLD = self::ESCAPE . '6';
    public const GRAY = self::ESCAPE . '7';
    public const DARK_GRAY = self::ESCAPE . '8';
    public const BLUE = self::ESCAPE . '9';
    public const GREEN = self::ESCAPE . 'a';
    public const AQUA = self::ESCAPE . 'b';
    public const RED = self::ESCAPE . 'c';
    public const LIGHT_PURPLE = self::ESCAPE . 'd';
    public const YELLOW = self::ESCAPE . 'e';
    public const WHITE = self::ESCAPE . 'f';
    public const MINECOIN_GOLD = self::ESCAPE . 'g';
    public const MATERIAL_QUARTZ = self::ESCAPE . 'h';
    public const MATERIAL_IRON = self::ESCAPE . 'i';
    public const MATERIAL_NETHERITE = self::ESCAPE . 'j';
    public const MATERIAL_REDSTONE = self::ESCAPE . 'm';
    public const MATERIAL_COPPER = self::ESCAPE . 'n';
    public const MATERIAL_GOLD = self::ESCAPE . 'p';
    public const MATERIAL_EMERALD = self::ESCAPE . 'q';
    public const MATERIAL_DIAMOND = self::ESCAPE . 's';
    public const MATERIAL_LAPIS = self::ESCAPE . 't';
    public const MATERIAL_AMETHYST = self::ESCAPE . 'u';
    public const MATERIAL_RESIN = self::ESCAPE . 'v';

    public const COLORS = [
        self::BLACK => self::BLACK,
        self::DARK_BLUE => self::DARK_BLUE,
        self::DARK_GREEN => self::DARK_GREEN,
        self::DARK_AQUA => self::DARK_AQUA,
        self::DARK_RED => self::DARK_RED,
        self::DARK_PURPLE => self::DARK_PURPLE,
        self::GOLD => self::GOLD,
        self::GRAY => self::GRAY,
        self::DARK_GRAY => self::DARK_GRAY,
        self::BLUE => self::BLUE,
        self::GREEN => self::GREEN,
        self::AQUA => self::AQUA,
        self::RED => self::RED,
        self::LIGHT_PURPLE => self::LIGHT_PURPLE,
        self::YELLOW => self::YELLOW,
        self::WHITE => self::WHITE,
        self::MINECOIN_GOLD => self::MINECOIN_GOLD,
        self::MATERIAL_QUARTZ => self::MATERIAL_QUARTZ,
        self::MATERIAL_IRON => self::MATERIAL_IRON,
        self::MATERIAL_NETHERITE => self::MATERIAL_NETHERITE,
        self::MATERIAL_REDSTONE => self::MATERIAL_REDSTONE,
        self::MATERIAL_COPPER => self::MATERIAL_COPPER,
        self::MATERIAL_GOLD => self::MATERIAL_GOLD,
        self::MATERIAL_EMERALD => self::MATERIAL_EMERALD,
        self::MATERIAL_DIAMOND => self::MATERIAL_DIAMOND,
        self::MATERIAL_LAPIS => self::MATERIAL_LAPIS,
        self::MATERIAL_AMETHYST => self::MATERIAL_AMETHYST,
        self::MATERIAL_RESIN => self::MATERIAL_RESIN,
    ];

    public const OBFUSCATED = self::ESCAPE . 'k';
    public const BOLD = self::ESCAPE . 'l';
    public const STRIKETHROUGH = '';
    public const UNDERLINE = '';
    public const ITALIC = self::ESCAPE . 'o';

    public const FORMATS = [
        self::OBFUSCATED => self::OBFUSCATED,
        self::BOLD => self::BOLD,
        self::ITALIC => self::ITALIC,
    ];

    public const RESET = self::ESCAPE . 'r';

    /** @return string[] */
    public static function tokenize(string $string): array
    {
        $parts = preg_split('/(' . preg_quote(self::ESCAPE, '/') . '[0-9a-v])/u', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            throw new \InvalidArgumentException('PCRE error while tokenizing text format string');
        }
        return $parts;
    }

    public static function clean(string $string, bool $removeFormat = true): string
    {
        $string = function_exists('mb_scrub') ? mb_scrub($string, 'UTF-8') : $string;
        $string = (string) preg_replace('/[\x{E000}-\x{F8FF}]/u', '', $string);
        if ($removeFormat) {
            $string = (string) preg_replace('/' . preg_quote(self::ESCAPE, '/') . '[0-9a-v]/u', '', $string);
        }
        $string = str_replace(self::ESCAPE, '', $string);
        $string = (string) preg_replace('/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/u', '', $string);
        return str_replace("\x1b", '', $string);
    }

    public static function colorize(string $string, string $placeholder = '&'): string
    {
        return (string) preg_replace('/' . preg_quote($placeholder, '/') . '([0-9a-v])/u', self::ESCAPE . '$1', $string);
    }

    public static function addBase(string $baseFormat, string $string): string
    {
        foreach (self::tokenize($baseFormat) as $part) {
            if (!isset(self::COLORS[$part]) && !isset(self::FORMATS[$part])) {
                throw new \InvalidArgumentException('Unexpected base format token "' . $part . '"');
            }
        }
        $base = self::RESET . $baseFormat;
        return $base . str_replace(self::RESET, $base, $string);
    }

    public static function javaToBedrock(string $string): string
    {
        return str_replace([self::ESCAPE . 'm', self::ESCAPE . 'n'], '', $string);
    }

    public static function toHTML(string $string): string
    {
        $styles = [
            self::BLACK => 'color:#000',
            self::DARK_BLUE => 'color:#00A',
            self::DARK_GREEN => 'color:#0A0',
            self::DARK_AQUA => 'color:#0AA',
            self::DARK_RED => 'color:#A00',
            self::DARK_PURPLE => 'color:#A0A',
            self::GOLD => 'color:#FA0',
            self::GRAY => 'color:#AAA',
            self::DARK_GRAY => 'color:#555',
            self::BLUE => 'color:#55F',
            self::GREEN => 'color:#5F5',
            self::AQUA => 'color:#5FF',
            self::RED => 'color:#F55',
            self::LIGHT_PURPLE => 'color:#F5F',
            self::YELLOW => 'color:#FF5',
            self::WHITE => 'color:#FFF',
            self::MINECOIN_GOLD => 'color:#dd0',
            self::BOLD => 'font-weight:bold',
            self::ITALIC => 'font-style:italic',
        ];
        $html = '';
        $open = 0;
        foreach (self::tokenize($string) as $token) {
            if ($token === self::RESET) {
                $html .= str_repeat('</span>', $open);
                $open = 0;
                continue;
            }
            if (isset($styles[$token])) {
                $html .= '<span style="' . $styles[$token] . '">';
                $open++;
                continue;
            }
            $html .= htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
        return $html . str_repeat('</span>', $open);
    }
}
