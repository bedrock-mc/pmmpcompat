<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item\upgrade;

final class R12ItemIdToBlockIdMap
{
    /** @var array<string, string> */
    private array $itemToBlock = [];
    /** @var array<string, string> */
    private array $blockToItem = [];

    /** @param array<string, string> $itemToBlock */
    public function __construct(array $itemToBlock = [])
    {
        foreach ($itemToBlock as $itemId => $blockId) {
            $this->add((string) $itemId, (string) $blockId);
        }
    }

    public function add(string $itemId, string $blockId): void
    {
        $this->itemToBlock[strtolower($itemId)] = $blockId;
        $this->blockToItem[strtolower($blockId)] = $itemId;
    }

    public function itemIdToBlockId(string $itemId): ?string
    {
        return $this->itemToBlock[strtolower($itemId)] ?? null;
    }

    public function blockIdToItemId(string $blockId): ?string
    {
        return $this->blockToItem[strtolower($blockId)] ?? null;
    }
}
