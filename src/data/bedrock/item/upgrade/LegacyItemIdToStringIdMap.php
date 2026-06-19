<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item\upgrade;

final class LegacyItemIdToStringIdMap
{
    /** @param array<int, string> $legacyToString */
    public function __construct(private array $legacyToString = [])
    {
    }

    public function legacyToString(int $legacyId): ?string
    {
        return $this->legacyToString[$legacyId] ?? null;
    }

    public function add(int $legacyId, string $stringId): void
    {
        $this->legacyToString[$legacyId] = $stringId;
    }
}
