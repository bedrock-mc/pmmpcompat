<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\data\bedrock\block\BlockStateData;

final class BlockStateDictionary
{
    /** @var list<BlockStateDictionaryEntry> */
    private array $states;
    /** @var array<string, int> */
    private array $nameMetaToRuntime = [];

    /** @param list<BlockStateDictionaryEntry|BlockStateData|array|string> $states */
    public function __construct(array $states = [])
    {
        if ($states === []) {
            $states = [
                new BlockStateDictionaryEntry('minecraft:air', [], 0),
                new BlockStateDictionaryEntry('minecraft:stone', [], 0),
                new BlockStateDictionaryEntry('minecraft:dirt', [], 0),
                new BlockStateDictionaryEntry('minecraft:grass', [], 0),
            ];
        }
        foreach ($states as $runtimeId => $state) {
            $entry = $this->normaliseEntry($state);
            $this->states[(int) $runtimeId] = $entry;
            $this->nameMetaToRuntime[$this->key($entry->getStateName(), $entry->getMeta())] = (int) $runtimeId;
        }
        ksort($this->states);
    }

    public static function loadFromString(string $payload): self { return self::loadPaletteFromString($payload); }

    public static function loadPaletteFromString(string $payload): self
    {
        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return new self();
        }
        $states = [];
        foreach ($decoded as $row) {
            if (is_array($row)) {
                $states[] = new BlockStateDictionaryEntry((string) ($row['name'] ?? 'minecraft:air'), is_array($row['states'] ?? null) ? $row['states'] : [], (int) ($row['meta'] ?? 0));
            }
        }
        return new self($states);
    }

    /** @return list<BlockStateDictionaryEntry> */
    public function getStates(): array { return array_values($this->states); }
    public function getMetaFromStateId(int $runtimeId): ?int { return $this->states[$runtimeId]?->getMeta() ?? null; }
    public function generateDataFromStateId(int $runtimeId): ?BlockStateData { return $this->states[$runtimeId]?->generateStateData() ?? null; }

    public function lookupStateIdFromData(BlockStateData $data): ?int
    {
        foreach ($this->states as $runtimeId => $entry) {
            if ($entry->generateStateData()->equals($data) || $entry->getStateName() === $data->getName()) {
                return $runtimeId;
            }
        }
        return null;
    }

    public function lookupStateIdFromIdMeta(string|int $name, int $meta = 0): ?int
    {
        return $this->nameMetaToRuntime[$this->key((string) $name, $meta)] ?? null;
    }

    private function normaliseEntry(mixed $state): BlockStateDictionaryEntry
    {
        if ($state instanceof BlockStateDictionaryEntry) {
            return $state;
        }
        if ($state instanceof BlockStateData) {
            return new BlockStateDictionaryEntry($state->getName(), $state->getStates(), 0);
        }
        if (is_array($state)) {
            return new BlockStateDictionaryEntry((string) ($state['name'] ?? 'minecraft:air'), is_array($state['states'] ?? null) ? $state['states'] : [], (int) ($state['meta'] ?? 0));
        }
        return new BlockStateDictionaryEntry((string) $state, [], 0);
    }

    private function key(string $name, int $meta): string { return $name . ':' . $meta; }
}
