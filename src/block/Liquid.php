<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;

class Liquid extends Block
{
    public const MAX_DECAY = 7;

    private int $decay = 0;
    private bool $falling = false;
    private bool $still = true;

    public function __construct()
    {
        parent::__construct('minecraft:liquid', 'Liquid');
    }

    public function addVelocityToEntity(mixed ...$args): ?Vector3 { return null; }
    public function canBeFlowedInto(): bool { return true; }
    public function canBeReplaced(): bool { return true; }
    public function getBucketEmptySound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBucketFillSound(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDecay(): int { return $this->decay; }
    public function setDecay(int $decay): self { $this->decay = max(0, min(self::MAX_DECAY, $decay)); return $this; }
    /** @return Item[] */
    public function getDropsForCompatibleTool(Item $item): array { return []; }
    public function getFlowDecayPerBlock(): int { return 1; }
    public function getFlowVector(): Vector3 { return Vector3::zero(); }
    public function getFlowingForm(): self { return (clone $this)->setStill(false); }
    public function getFluidHeightPercent(): float { return 1.0; }
    public function getMinAdjacentSourcesToFormSource(): int { return 2; }
    public function getStillForm(): self { return (clone $this)->setStill(true); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEntityCollision(): bool { return false; }
    public function isFalling(): bool { return $this->falling; }
    public function setFalling(bool $falling): self { $this->falling = $falling; return $this; }
    public function isSolid(): bool { return false; }
    public function isSource(): bool { return $this->decay === 0; }
    public function isStill(): bool { return $this->still; }
    public function setStill(bool $still): self { $this->still = $still; return $this; }
    public function onNearbyBlockChange(): void {}
    public function onScheduledUpdate(): void {}
    public function readStateFromWorld(): self { return $this; }
    public function tickRate(): int { return 5; }
    public function asItem(): Item { return VanillaItems::AIR(); }
}
