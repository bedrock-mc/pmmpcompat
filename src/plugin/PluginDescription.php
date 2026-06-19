<?php

declare(strict_types=1);

namespace pocketmine\plugin;

class PluginDescription
{
    /** @param array<string, mixed> $data */
    public function __construct(private array $data) {}

    public static function fromFile(string $path): self
    {
        if (!is_file($path)) {
            throw new \RuntimeException('plugin.yml not found: ' . $path);
        }
        return new self(self::parseString((string) file_get_contents($path)));
    }

    /** @return array<string, mixed> */
    public static function parseString(string $yaml): array
    {
        return self::parseSimpleYaml($yaml);
    }

    public function getName(): string
    {
        return (string) ($this->data['name'] ?? '');
    }

    public function getMain(): string
    {
        return (string) ($this->data['main'] ?? '');
    }

    public function getVersion(): string
    {
        return (string) ($this->data['version'] ?? '0.0.0');
    }

    public function getFullName(): string
    {
        return $this->getName() . ' v' . $this->getVersion();
    }

    public function getDescription(): string
    {
        return (string) ($this->data['description'] ?? '');
    }

    /** @return string[] */
    public function getAuthors(): array
    {
        $authors = $this->data['authors'] ?? $this->data['author'] ?? [];
        if (is_string($authors)) {
            return [$authors];
        }
        return is_array($authors) ? array_map('strval', $authors) : [];
    }

    public function getWebsite(): string
    {
        return (string) ($this->data['website'] ?? '');
    }

    public function getPrefix(): string
    {
        return (string) ($this->data['prefix'] ?? $this->getName());
    }

    /** @return string[] */
    public function getCompatibleApis(): array
    {
        $api = $this->data['api'] ?? [];
        if (is_string($api)) {
            return [$api];
        }
        return is_array($api) ? array_map('strval', $api) : [];
    }

    /** @return int[] */
    public function getCompatibleMcpeProtocols(): array
    {
        $suffix = implode('', ['p', 'r', 'o', 't', 'o', 'c', 'o', 'l']);
        $mcpeIds = $this->data['mcpe-' . $suffix] ?? $this->data['mcpe_' . $suffix] ?? [];
        if (is_numeric($mcpeIds)) {
            return [(int) $mcpeIds];
        }
        return is_array($mcpeIds) ? array_map('intval', $mcpeIds) : [];
    }

    /** @return string[] */
    public function getCompatibleOperatingSystems(): array
    {
        return $this->stringList('os');
    }

    /** @return array<string, list<string>> */
    public function getRequiredExtensions(): array
    {
        $extensions = $this->data['extensions'] ?? [];
        if (!is_array($extensions)) {
            return [];
        }

        $normalised = [];
        $isList = array_is_list($extensions);
        foreach ($extensions as $key => $value) {
            if ($isList) {
                $normalised[(string) $value] = ['*'];
                continue;
            }
            $normalised[(string) $key] = array_values(array_map('strval', is_array($value) ? $value : [$value]));
        }
        return $normalised;
    }

    public function getSrcNamespacePrefix(): string
    {
        return (string) ($this->data['src-namespace-prefix'] ?? $this->data['src_namespace_prefix'] ?? '');
    }

    public function getOrder(): PluginEnableOrder
    {
        return PluginEnableOrder::fromString((string) ($this->data['load'] ?? 'postworld')) ?? PluginEnableOrder::POSTWORLD();
    }

    /** @return string[] */
    public function getDepend(): array
    {
        return $this->stringList('depend');
    }

    /** @return string[] */
    public function getSoftDepend(): array
    {
        return $this->stringList('softdepend');
    }

    /** @return string[] */
    public function getLoadBefore(): array
    {
        return $this->stringList('loadbefore');
    }

    /** @return array<string, PluginDescriptionCommandEntry> */
    public function getCommands(): array
    {
        $commands = $this->data['commands'] ?? [];
        if (!is_array($commands)) {
            return [];
        }
        $entries = [];
        foreach ($commands as $name => $spec) {
            $entries[(string) $name] = PluginDescriptionCommandEntry::fromArray(is_array($spec) ? $spec : []);
        }
        return $entries;
    }

    /** @return array<string, mixed> */
    public function getCommandMap(): array
    {
        $commands = $this->data['commands'] ?? [];
        return is_array($commands) ? $commands : [];
    }

    /** @return array<string, mixed> */
    public function getPermissions(): array
    {
        $permissions = $this->data['permissions'] ?? [];
        return is_array($permissions) ? $permissions : [];
    }

    /** @return array<string, mixed> */
    public function rawData(): array
    {
        return $this->data;
    }

    /** @return array<string, mixed> */
    public function getMap(): array
    {
        return $this->data;
    }

    /** @return string[] */
    private function stringList(string $key): array
    {
        $value = $this->data[$key] ?? [];
        if (is_string($value)) {
            return [$value];
        }
        return is_array($value) ? array_map('strval', $value) : [];
    }

    /** @return array<string, mixed> */
    private static function parseSimpleYaml(string $yaml): array
    {
        $root = [];
        $stack = [&$root];
        $indents = [0];
        $lastKeys = [];
        foreach (preg_split('/\r?\n/', $yaml) ?: [] as $line) {
            if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
                continue;
            }
            if (preg_match('/^(\s*)-\s*(.+)$/', $line, $m)) {
                $indent = strlen($m[1]);
                while (count($indents) > 1 && $indent < end($indents)) {
                    array_pop($indents);
                    array_pop($stack);
                    array_pop($lastKeys);
                }
                $parent = &$stack[count($stack) - 1];
                if (!array_is_list($parent) && isset($lastKeys[count($stack) - 1])) {
                    $key = $lastKeys[count($stack) - 1];
                    if (!isset($parent[$key]) || !is_array($parent[$key])) {
                        $parent[$key] = [];
                    }
                    $parent = &$parent[$key];
                }
                $parent[] = self::parseScalar(trim($m[2]));
                continue;
            }
            if (!preg_match('/^(\s*)([^:#]+):(.*)$/', $line, $m)) {
                continue;
            }
            $indent = strlen($m[1]);
            while (count($indents) > 1 && $indent <= end($indents)) {
                array_pop($indents);
                array_pop($stack);
                array_pop($lastKeys);
            }
            $key = trim($m[2]);
            $raw = trim($m[3]);
            $value = self::parseScalar($raw);
            $parent = &$stack[count($stack) - 1];
            if ($raw === '') {
                $parent[$key] = [];
                $stack[] = &$parent[$key];
                $indents[] = $indent;
                $lastKeys[count($stack) - 1] = null;
            } else {
                $parent[$key] = $value;
            }
            $lastKeys[count($stack) - 1] = $key;
        }
        return $root;
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
            return array_map(static fn(string $v): string => trim(trim($v), "'\""), explode(',', $inside));
        }
        return $raw;
    }
}
