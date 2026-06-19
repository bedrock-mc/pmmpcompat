<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class FallingBlock extends Entity
{
    private Block $block;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $nbt = null;
        $block = null;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif ($arg instanceof Block && $block === null) {
                $block = $arg;
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            }
        }
        $this->block = clone ($block ?? VanillaBlocks::AIR());
        parent::__construct($location, $nbt);
    }

    public function attack(mixed ...$args): mixed { return null; }
    public function canBeMovedByCurrents(mixed ...$args): bool { return false; }
    public function canCollideWith(mixed ...$args): bool { return false; }
    public function getBlock(mixed ...$args): Block { return clone $this->block; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:falling_block'; }
    public function getOffsetPosition(mixed ...$args): Vector3
    {
        $vector = ($args[0] ?? null) instanceof Vector3 ? $args[0] : ($this->getPosition() ?? Vector3::zero());
        return $vector->add(0, 0.49, 0);
    }
    public function getPickedItem(mixed ...$args): ?Item { return $this->block->asItem(); }
    public static function parseBlockNBT(mixed ...$args): Block { return VanillaBlocks::AIR(); }
    public function saveNBT(mixed ...$args): CompoundTag { return parent::saveNBT(); }
}
