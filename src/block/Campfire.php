<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Campfire extends Block
{
    private int $cookingTime = 600;
    private Inventory $inventory;

    public function __construct()
    {
        parent::__construct('minecraft:campfire', 'Campfire');
        $this->inventory = new Inventory(4);
    }

    public function getCookingTime(): int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(int $cookingTime): self
    {
        $this->cookingTime = max(0, $cookingTime);
        return $this;
    }

    /** @return Item[] */
    public function getDropsForCompatibleTool(Item $item): array
    {
        return [VanillaItems::AIR()];
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getLightLevel(): int
    {
        return 15;
    }

    public function getSupportType(mixed ...$args): mixed
    {
        return null;
    }

    public function hasEntityCollision(): bool
    {
        return true;
    }

    public function isAffectedBySilkTouch(): bool
    {
        return false;
    }

    public function onEntityInside(mixed ...$args): bool
    {
        return false;
    }

    public function onInteract(mixed ...$args): bool
    {
        return false;
    }

    public function onNearbyBlockChange(): void {}

    public function onProjectileHit(mixed ...$args): void {}

    public function onScheduledUpdate(): void {}

    public function place(mixed ...$args): bool
    {
        return true;
    }

    public function readStateFromWorld(): self
    {
        return $this;
    }

    public function writeStateToWorld(): void {}
}
