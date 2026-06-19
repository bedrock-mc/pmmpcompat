<?php

declare(strict_types=1);

namespace pocketmine\utils;

class Config
{
    public const DETECT = -1;
    public const PROPERTIES = 0;
    public const CNF = self::PROPERTIES;
    public const JSON = 1;
    public const YAML = 2;
    public const SERIALIZED = 4;
    public const ENUM = 5;
    public const ENUMERATION = self::ENUM;

    /** @var array<string, mixed> */
    private array $data;
    /** @var array<string, mixed> */
    private array $nestedCache = [];
    private int $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
    private bool $changed = false;

    /** @var array<string, int> */
    public static array $formats = [
        'properties' => self::PROPERTIES,
        'cnf' => self::CNF,
        'conf' => self::CNF,
        'config' => self::CNF,
        'json' => self::JSON,
        'js' => self::JSON,
        'yml' => self::YAML,
        'yaml' => self::YAML,
        'sl' => self::SERIALIZED,
        'serialize' => self::SERIALIZED,
        'txt' => self::ENUM,
        'list' => self::ENUM,
        'enum' => self::ENUM,
    ];

    /** @param array<string, mixed> $default */
    public function __construct(private string $file, private int $type = self::DETECT, array $default = [])
    {
        if ($this->type === self::DETECT) {
            $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
            if (!isset(self::$formats[$extension])) {
                throw new \InvalidArgumentException('Cannot detect config type of ' . $this->file);
            }
            $this->type = self::$formats[$extension];
        }
        if (is_file($file)) {
            $this->data = $this->load((string) file_get_contents($file));
        } else {
            $this->data = $default;
            if ($default !== []) {
                $this->changed = true;
                $this->save();
            }
        }
    }

    public function reload(): void
    {
        $this->nestedCache = [];
        $this->data = is_file($this->file) ? $this->load((string) file_get_contents($this->file)) : [];
        $this->changed = false;
    }

    public function hasChanged(): bool
    {
        return $this->changed;
    }

    public function setChanged(bool $changed = true): void
    {
        $this->changed = $changed;
    }

    public static function fixYAMLIndexes(string $str): string
    {
        return preg_replace("#^( *)(y|Y|yes|Yes|YES|n|N|no|No|NO|true|True|TRUE|false|False|FALSE|on|On|ON|off|Off|OFF)( *)\:#m", "$1\"$2\"$3:", $str) ?? $str;
    }

    public function getPath(): string
    {
        return $this->file;
    }

    public function setJsonOptions(int $options): self
    {
        $this->assertJsonConfig('set JSON options');
        $this->jsonOptions = $options;
        $this->changed = true;
        return $this;
    }

    public function enableJsonOption(int $option): self
    {
        $this->assertJsonConfig('enable JSON option');
        $this->jsonOptions |= $option;
        $this->changed = true;
        return $this;
    }

    public function disableJsonOption(int $option): self
    {
        $this->assertJsonConfig('disable JSON option');
        $this->jsonOptions &= ~$option;
        $this->changed = true;
        return $this;
    }

    public function getJsonOptions(): int
    {
        $this->assertJsonConfig('get JSON options');
        return $this->jsonOptions;
    }

    public function __get(string $key): mixed
    {
        return $this->get($key);
    }

    public function __set(string $key, mixed $value): void
    {
        $this->set($key, $value);
    }

    public function __isset(string $key): bool
    {
        return $this->exists($key);
    }

    public function __unset(string $key): void
    {
        $this->remove($key);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
        $this->changed = true;
        foreach (array_keys($this->nestedCache) as $nestedKey) {
            if (str_starts_with($nestedKey, $key . '.')) {
                unset($this->nestedCache[$nestedKey]);
            }
        }
    }

    public function setNested(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $node = &$this->data;
        while (count($parts) > 1) {
            $part = array_shift($parts);
            if (!isset($node[$part]) || !is_array($node[$part])) {
                $node[$part] = [];
            }
            $node = &$node[$part];
        }
        $node[array_shift($parts)] = $value;
        $this->nestedCache = [];
        $this->changed = true;
    }

    public function getNested(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->nestedCache)) {
            return $this->nestedCache[$key];
        }
        $node = $this->data;
        foreach (explode('.', $key) as $part) {
            if (!is_array($node) || !array_key_exists($part, $node)) {
                return $default;
            }
            $node = $node[$part];
        }
        return $this->nestedCache[$key] = $node;
    }

    public function removeNested(string $key): void
    {
        $parts = explode('.', $key);
        $node = &$this->data;
        while (count($parts) > 1) {
            $part = array_shift($parts);
            if (!isset($node[$part]) || !is_array($node[$part])) {
                return;
            }
            $node = &$node[$part];
        }
        unset($node[array_shift($parts)]);
        $this->nestedCache = [];
        $this->changed = true;
    }

    public function exists(string $key, bool $lowercase = false): bool
    {
        if ($lowercase) {
            return array_key_exists(strtolower($key), array_change_key_case($this->data, CASE_LOWER));
        }
        return array_key_exists($key, $this->data);
    }

    /** @return array<string, mixed> */
    public function getAll(bool $keys = false): array
    {
        return $keys ? array_keys($this->data) : $this->data;
    }

    /** @param array<string, mixed> $values */
    public function setAll(array $values): void
    {
        $this->data = $values;
        $this->nestedCache = [];
        $this->changed = true;
    }

    /** @param array<string, mixed> $defaults */
    public function setDefaults(array $defaults): void
    {
        $this->fillDefaults($defaults, $this->data);
    }

    public function remove(string $key): void
    {
        unset($this->data[$key]);
        $this->nestedCache = [];
        $this->changed = true;
    }

    public function save(): void
    {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($this->file, $this->dump($this->data));
        $this->changed = false;
    }

    /** @return string[] */
    public static function parseList(string $content): array
    {
        $result = [];
        foreach (explode("\n", trim(str_replace("\r\n", "\n", $content))) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $result[] = $line;
            }
        }
        return $result;
    }

    /** @param array<int, string|int> $entries */
    public static function writeList(array $entries): string
    {
        return implode("\n", $entries);
    }

    /** @param array<string, string|int|float|bool> $config */
    public static function writeProperties(array $config): string
    {
        $content = "#Properties Config file\r\n#" . date('D M j H:i:s T Y') . "\r\n";
        foreach ($config as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'on' : 'off';
            }
            $content .= $key . '=' . $value . "\r\n";
        }
        return $content;
    }

    /** @return array<string, string|int|float|bool> */
    public static function parseProperties(string $content): array
    {
        $result = [];
        if (preg_match_all('/^\s*([a-zA-Z0-9\-_\.]+)[ \t]*=([^\r\n]*)/um', $content, $matches) > 0) {
            foreach ($matches[1] as $i => $key) {
                $result[$key] = self::parseScalar(trim($matches[2][$i]));
            }
        }
        return $result;
    }

    /** @return array<string, mixed> */
    private function load(string $contents): array
    {
        return match ($this->type) {
            self::JSON => is_array($decoded = json_decode($contents, true)) ? $decoded : [],
            self::PROPERTIES => self::parseProperties($contents),
            self::SERIALIZED => is_array($decoded = @unserialize($contents)) ? $decoded : [],
            self::ENUM => array_fill_keys(self::parseList($contents), true),
            default => $this->parseYaml($contents),
        };
    }

    /** @param array<string, mixed> $data */
    private function fillDefaults(array $defaults, array &$data): int
    {
        $changed = 0;
        foreach ($defaults as $key => $value) {
            if (is_array($value)) {
                if (!isset($data[$key]) || !is_array($data[$key])) {
                    $data[$key] = [];
                }
                $changed += $this->fillDefaults($value, $data[$key]);
            } elseif (!array_key_exists($key, $data)) {
                $data[$key] = $value;
                $changed++;
            }
        }
        if ($changed > 0) {
            $this->changed = true;
        }
        return $changed;
    }

    /** @param array<string, mixed> $data */
    private function dump(array $data): string
    {
        return match ($this->type) {
            self::JSON => json_encode($data, $this->jsonOptions) . PHP_EOL,
            self::PROPERTIES => self::writeProperties($data),
            self::SERIALIZED => serialize($data),
            self::ENUM => self::writeList(array_keys($data)),
            default => $this->dumpYaml($data),
        };
    }

    /** @return array<string, mixed> */
    private function parseYaml(string $contents): array
    {
        $root = [];
        /** @var array<int, array{indent:int, ref:array<string, mixed>}> $stack */
        $stack = [['indent' => -1, 'ref' => &$root]];
        foreach (preg_split('/\r?\n/', self::fixYAMLIndexes($contents)) ?: [] as $line) {
            if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
                continue;
            }
            $indent = strlen($line) - strlen(ltrim($line, ' '));
            $trimmed = trim($line);
            while (count($stack) > 1 && $indent <= $stack[array_key_last($stack)]['indent']) {
                array_pop($stack);
            }
            $parent = &$stack[array_key_last($stack)]['ref'];
            if (str_starts_with($trimmed, '- ')) {
                $parent[] = $this->parseScalar(substr($trimmed, 2));
                unset($parent);
                continue;
            }
            if (!preg_match('/^([^:#]+):\s*(.*)$/', $trimmed, $matches)) {
                unset($parent);
                continue;
            }
            $key = trim($matches[1], " \t'\"");
            $rawValue = trim($matches[2]);
            if ($rawValue === '') {
                $parent[$key] = [];
                $stack[] = ['indent' => $indent, 'ref' => &$parent[$key]];
            } else {
                $parent[$key] = $this->parseScalar($rawValue);
            }
            unset($parent);
        }
        return $root;
    }

    /** @param array<string, mixed> $data */
    private function dumpYaml(array $data, int $indent = 0): string
    {
        $lines = [];
        $prefix = str_repeat(' ', $indent);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $lines[] = $prefix . $key . ':';
                $isList = array_is_list($value);
                foreach ($value as $childKey => $childValue) {
                    if ($isList) {
                        $lines[] = $prefix . '  - ' . $this->formatScalar($childValue);
                    } elseif (is_array($childValue)) {
                        $lines[] = $prefix . '  ' . $childKey . ':';
                        $lines[] = rtrim($this->dumpYaml($childValue, $indent + 4));
                    } else {
                        $lines[] = $prefix . '  ' . $childKey . ': ' . $this->formatScalar($childValue);
                    }
                }
            } else {
                $lines[] = $prefix . $key . ': ' . $this->formatScalar($value);
            }
        }
        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private static function parseScalar(string $raw): mixed
    {
        $raw = trim($raw, " \t");
        if ($raw === '') {
            return '';
        }
        if (($raw[0] === '"' && str_ends_with($raw, '"')) || ($raw[0] === "'" && str_ends_with($raw, "'"))) {
            return substr($raw, 1, -1);
        }
        if ($raw[0] === '[' && str_ends_with($raw, ']')) {
            $inside = trim(substr($raw, 1, -1));
            if ($inside === '') {
                return [];
            }
            return array_map(static fn(string $v): mixed => self::parseScalar($v), explode(',', $inside));
        }
        if ($raw === '{}') {
            return [];
        }
        $lower = strtolower($raw);
        if (in_array($lower, ['true', 'yes', 'on'], true)) {
            return true;
        }
        if (in_array($lower, ['false', 'no', 'off'], true)) {
            return false;
        }
        if (is_numeric($raw)) {
            return str_contains($raw, '.') ? (float) $raw : (int) $raw;
        }
        return trim($raw, "'\"");
    }

    private function formatScalar(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        return (string) $value;
    }

    private function assertJsonConfig(string $action): void
    {
        if ($this->type !== self::JSON) {
            throw new \RuntimeException('Attempt to ' . $action . ' for non-JSON config');
        }
    }
}
