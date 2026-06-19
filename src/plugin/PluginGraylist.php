<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\utils\Utils;

/**
 * PMMP-style plugin allow/deny list.
 */
class PluginGraylist
{
    /** @var array<string, true> */
    private array $plugins;

    public function __construct(array $plugins = [], private bool $isWhitelist = false)
    {
        $this->plugins = array_fill_keys(array_map('strval', $plugins), true);
    }

    /** @return list<string> */
    public function getPlugins(): array
    {
        return array_keys($this->plugins);
    }

    public function isWhitelist(): bool
    {
        return $this->isWhitelist;
    }

    public function isAllowed(string $name): bool
    {
        return $this->isWhitelist === isset($this->plugins[$name]);
    }

    public static function fromArray(array $array): self
    {
        if (!isset($array['mode']) || ($array['mode'] !== 'whitelist' && $array['mode'] !== 'blacklist')) {
            throw new \InvalidArgumentException('"mode" must be set');
        }

        $plugins = [];
        if (isset($array['plugins'])) {
            if (!is_array($array['plugins'])) {
                throw new \InvalidArgumentException('"plugins" must be an array');
            }
            foreach (Utils::promoteKeys($array['plugins']) as $k => $v) {
                if (!is_string($v) && !is_int($v) && !is_float($v)) {
                    throw new \InvalidArgumentException('"plugins" contains invalid element at position ' . $k);
                }
                $plugins[] = (string) $v;
            }
        }

        return new self($plugins, $array['mode'] === 'whitelist');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'mode' => $this->isWhitelist ? 'whitelist' : 'blacklist',
            'plugins' => $this->plugins,
        ];
    }
}
