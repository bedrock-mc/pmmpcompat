<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item;

class BlockItemIdMap
{
    /** @var array<string, string> */
    private array $blockToItemId;
    /** @var array<string, string> */
    private array $itemToBlockId;
    private static ?self $instance = null;

    /** @param array<string, string> $blockToItemId */
    public function __construct(array $blockToItemId = [])
    {
        $this->blockToItemId = $blockToItemId + [
            'minecraft:air' => 'minecraft:air',
            'minecraft:stone' => 'minecraft:stone',
            'minecraft:dirt' => 'minecraft:dirt',
            'minecraft:grass' => 'minecraft:grass',
        ];
        $this->itemToBlockId = array_flip($this->blockToItemId);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function lookupBlockId(string $itemId): ?string
    {
        return $this->itemToBlockId[$itemId] ?? null;
    }

    public function lookupItemId(string $blockId): ?string
    {
        return $this->blockToItemId[$blockId] ?? null;
    }
}
