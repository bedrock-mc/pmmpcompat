<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;

final class ItemTranslator
{
    public const NO_BLOCK_RUNTIME_ID = 0;

    public function __construct(private array $nameToId = [], private array $idToName = [])
    {
        if ($this->nameToId === []) {
            $this->nameToId = ['minecraft:air' => 0, 'minecraft:stone' => 1, 'minecraft:dirt' => 2, 'minecraft:diamond' => 264, 'minecraft:diamond_sword' => 276];
        }
        if ($this->idToName === []) {
            $this->idToName = array_flip($this->nameToId);
        }
    }

    /** @return array{int, int, ?int}|null */
    public function toNetworkIdQuiet(Item $item): ?array
    {
        try {
            return $this->toNetworkId($item);
        } catch (TypeConversionException) {
            return null;
        }
    }

    /** @return array{int, int, ?int} */
    public function toNetworkId(Item $item): array
    {
        $typeId = $item->getTypeId();
        if (!isset($this->nameToId[$typeId])) {
            throw new TypeConversionException("Unknown item type $typeId");
        }
        return [$this->nameToId[$typeId], 0, $item->canBePlaced() ? 1 : self::NO_BLOCK_RUNTIME_ID];
    }

    public function toNetworkNbt(Item $item): CompoundTag
    {
        return CompoundTag::create()->setString('Name', $item->getTypeId())->setByte('Count', $item->getCount());
    }

    public function fromNetworkId(int $networkId, int $networkMeta = 0, int $networkBlockRuntimeId = self::NO_BLOCK_RUNTIME_ID): Item
    {
        $name = $this->idToName[$networkId] ?? null;
        if ($name === null) {
            throw new TypeConversionException("Unknown network item ID $networkId");
        }
        return new Item($name, ucwords(str_replace(['minecraft:', '_'], ['', ' '], $name)));
    }
}
