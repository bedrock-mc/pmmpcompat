<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\upgrade;

use pocketmine\data\bedrock\block\BlockStateData;

final class BlockIdMetaUpgrader
{
    /** @var array<int, string> */
    private array $intIdToStringId = [];
    /** @var array<string, array<int, BlockStateData>> */
    private array $idMetaToState = [];

    /** @param array<int, string> $legacyIntToStringId @param array<string, array<int, BlockStateData>> $idMetaToState */
    public function __construct(array|LegacyBlockIdToStringIdMap $legacyIntToStringId = [], array $idMetaToState = [])
    {
        if ($legacyIntToStringId instanceof LegacyBlockIdToStringIdMap) {
            $legacyIntToStringId = [];
        }
        foreach ($legacyIntToStringId as $legacyId => $stringId) {
            $this->addIntIdToStringIdMapping((int) $legacyId, (string) $stringId);
        }
        foreach ($idMetaToState as $stringId => $metaMap) {
            foreach ($metaMap as $meta => $state) {
                $this->addIdMetaToStateMapping((string) $stringId, (int) $meta, $state);
            }
        }
    }

    public static function loadFromString(string $raw): self
    {
        $json = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($json)) {
            throw new \RuntimeException('Expected block ID/meta map object');
        }
        $upgrader = new self();
        foreach (($json['legacyIntToStringId'] ?? []) as $legacyId => $stringId) {
            $upgrader->addIntIdToStringIdMapping((int) $legacyId, (string) $stringId);
        }
        foreach (($json['idMetaToState'] ?? []) as $stringId => $metaMap) {
            foreach ((array) $metaMap as $meta => $stateData) {
                $upgrader->addIdMetaToStateMapping((string) $stringId, (int) $meta, BlockStateData::fromNbt($stateData));
            }
        }
        return $upgrader;
    }

    public function addIntIdToStringIdMapping(int $legacyId, string $stringId): void
    {
        $this->intIdToStringId[$legacyId] = $stringId;
    }

    public function addIdMetaToStateMapping(string $stringId, int $meta, BlockStateData $state): void
    {
        $this->idMetaToState[strtolower($stringId)][$meta] = $state;
    }

    public function fromIntIdMeta(int $legacyId, int $meta): ?BlockStateData
    {
        $stringId = $this->intIdToStringId[$legacyId] ?? null;
        return $stringId === null ? null : $this->fromStringIdMeta($stringId, $meta);
    }

    public function fromStringIdMeta(string $stringId, int $meta): ?BlockStateData
    {
        $metaMap = $this->idMetaToState[strtolower($stringId)] ?? null;
        if ($metaMap === null) {
            return null;
        }
        return $metaMap[$meta] ?? $metaMap[0] ?? null;
    }
}
