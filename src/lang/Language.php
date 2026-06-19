<?php

declare(strict_types=1);

namespace pocketmine\lang;

class Language
{
    public const FALLBACK_LANGUAGE = 'eng';

    protected string $langName;

    /** @var array<string, string> */
    protected array $lang = [];

    /** @var array<string, string> */
    protected array $fallbackLang = [];

    /** @return array<string, string> */
    public static function getLanguageList(string $path = ''): array
    {
        $path = $path !== '' ? $path : (defined('pocketmine\\LOCALE_DATA_PATH') ? LOCALE_DATA_PATH : '');
        if ($path !== '' && is_dir($path)) {
            $result = [];
            foreach (scandir($path) ?: [] as $file) {
                if (!str_ends_with($file, '.ini')) {
                    continue;
                }
                $code = explode('.', $file, 2)[0];
                try {
                    $strings = self::loadLang($path, $code);
                    $result[$code] = $strings[KnownTranslationKeys::LANGUAGE_NAME] ?? $code;
                } catch (LanguageNotFoundException) {
                }
            }
            if ($result !== []) {
                return $result;
            }
        }

        return [self::FALLBACK_LANGUAGE => 'English'];
    }

    public function __construct(string $lang, ?string $path = null, string $fallback = self::FALLBACK_LANGUAGE)
    {
        $this->langName = strtolower($lang);
        $path ??= defined('pocketmine\\LOCALE_DATA_PATH') ? LOCALE_DATA_PATH : null;
        if ($path !== null && is_dir($path)) {
            try {
                $this->lang = self::loadLang($path, $this->langName);
            } catch (LanguageNotFoundException $e) {
                if ($this->langName !== $fallback) {
                    throw $e;
                }
            }
            try {
                $this->fallbackLang = self::loadLang($path, $fallback);
            } catch (LanguageNotFoundException) {
                $this->fallbackLang = [];
            }
        }
        $this->fallbackLang += [
            KnownTranslationKeys::LANGUAGE_NAME => 'English',
            KnownTranslationKeys::CHAT_TYPE_TEXT => '<{%0}> {%1}',
        ];
    }

    public function getName(): string
    {
        return $this->get(KnownTranslationKeys::LANGUAGE_NAME);
    }

    public function getLang(): string
    {
        return $this->langName;
    }

    /** @return array<string, string> */
    protected static function loadLang(string $path, string $languageCode): array
    {
        $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $languageCode . '.ini';
        if (is_file($file)) {
            $strings = parse_ini_file($file, false, INI_SCANNER_RAW);
            if (is_array($strings) && $strings !== []) {
                return array_map('stripcslashes', $strings);
            }
        }
        throw new LanguageNotFoundException('Language "' . $languageCode . '" not found');
    }

    /** @param array<int|string, float|int|string|Translatable> $params */
    public function translateString(string $str, array $params = [], ?string $onlyPrefix = null): string
    {
        $baseText = ($onlyPrefix === null || str_starts_with($str, $onlyPrefix)) ? $this->internalGet($str) : null;
        $baseText ??= $this->parseTranslation($str, $onlyPrefix);
        foreach ($params as $i => $p) {
            $replacement = $p instanceof Translatable ? $this->translate($p) : (string) $p;
            $baseText = str_replace('{%' . $i . '}', $replacement, $baseText);
        }
        return $baseText;
    }

    public function translate(Translatable $c): string
    {
        return $this->translateString($c->getText(), $c->getParameters());
    }

    protected function internalGet(string $id): ?string
    {
        return $this->lang[$id] ?? $this->fallbackLang[$id] ?? null;
    }

    public function get(string $id): string
    {
        return $this->internalGet($id) ?? $id;
    }

    /** @return array<string, string> */
    public function getAll(): array
    {
        return $this->lang;
    }

    protected function parseTranslation(string $text, ?string $onlyPrefix = null): string
    {
        return preg_replace_callback('/%([A-Za-z0-9_.-]+)/', function (array $matches) use ($onlyPrefix): string {
            $key = $matches[1];
            if ($onlyPrefix !== null && !str_starts_with($key, ltrim($onlyPrefix, '%'))) {
                return $matches[0];
            }
            return $this->internalGet($key) ?? $matches[0];
        }, $text) ?? $text;
    }
}
