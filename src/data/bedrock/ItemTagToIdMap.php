<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock;

class ItemTagToIdMap
{
    use \pocketmine\utils\SingletonTrait;

    /** @var array<string, array<string, true>> */
    private array $tags = [];
    public function __construct(mixed ...$args) { foreach (($args[0] ?? []) as $tag => $ids) { foreach ((array) $ids as $id) { $this->addIdToTag((string) $tag, (string) $id); } } }
    public function addIdToTag(mixed ...$args): mixed { $this->tags[(string) ($args[0] ?? '')][(string) ($args[1] ?? '')] = true; return null; }
    public function getIdsForTag(mixed ...$args): mixed { return array_keys($this->tags[(string) ($args[0] ?? '')] ?? []); }
    public function tagContainsId(mixed ...$args): mixed { return isset($this->tags[(string) ($args[0] ?? '')][(string) ($args[1] ?? '')]); }}
