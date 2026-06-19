<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

abstract class LegacyToStringIdMap
{
    /** @var array<int, string> */
    private array $legacyToString = [];
    public function __construct(mixed ...$args) { if (isset($args[0]) && is_array($args[0])) { foreach ($args[0] as $legacy => $string) { $this->add((string) $string, (int) $legacy); } } }
    public function add(mixed ...$args): mixed { $this->legacyToString[(int) ($args[1] ?? 0)] = (string) ($args[0] ?? ''); return null; }
    public function getLegacyToStringMap(mixed ...$args): mixed { return $this->legacyToString; }
    public function legacyToString(mixed ...$args): mixed { return $this->legacyToString[(int) ($args[0] ?? 0)] ?? null; }}
