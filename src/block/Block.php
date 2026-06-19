<?php

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

class Block
{
    public const EMPTY_STATE_ID = 0;
    public const INTERNAL_STATE_DATA_BITS = 17;
    public const INTERNAL_STATE_DATA_MASK = 0x1ffff;

    public function __construct(
        private string $typeId,
        private string $name,
        private ?Vector3 $position = null,
    ) {}

    /** @var array<string, mixed> */
    private array $compatState = [];
    private static int $nextCompatTypeId = 100000;

    public function __clone() {}
    public function __toString(): string { return $this->name . ' (' . $this->typeId . ')'; }
    public function getTypeId(): string { return $this->typeId; }
    public function getStateId(): int { return crc32($this->typeId); }
    public function getIdInfo(): array { return ['type_id' => $this->typeId, 'state_id' => $this->getStateId()]; }
    public function getName(): string { return $this->name; }
    public function getPosition(): ?Vector3 { return $this->position; }
    public function position(World $world, int $x, int $y, int $z): void { $this->position = new Position($x, $y, $z, $world); }
    public function hasSameTypeId(self $other): bool { return $this->typeId === $other->typeId; }
    public function isSameState(self $other): bool { return $this->typeId === $other->typeId; }
    /** @return string[] */
    public function getTypeTags(): array { return []; }
    public function hasTypeTag(string $tag): bool { return false; }
    public function asItem(): Item { return match ($this->typeId) {
        'minecraft:stone' => VanillaItems::STONE(),
        'minecraft:dirt' => VanillaItems::DIRT(),
        default => VanillaItems::AIR(),
    }; }
    public function describeBlockItemState(mixed ...$args): void {}
    public function generateStatePermutations(): \Generator { yield clone $this; }
    public function readStateFromWorld(): self { return $this; }
    public function writeStateToWorld(): void {}
    public function canBePlaced(): bool { return $this->typeId !== 'minecraft:air'; }
    public function canBeReplaced(): bool { return $this->typeId === 'minecraft:air'; }
    public function canBePlacedAt(mixed ...$args): bool { return $this->canBePlaced(); }
    public function place(mixed ...$args): bool { return $this->canBePlaced(); }
    public function onPostPlace(): void {}
    public function getBreakInfo(): array { return []; }
    /** @return string[] */
    public function getEnchantmentTags(): array { return []; }
    public function onBreak(mixed ...$args): bool { return true; }
    public function onNearbyBlockChange(): void {}
    public function ticksRandomly(): bool { return false; }
    public function onRandomTick(): void {}
    public function onScheduledUpdate(): void {}
    public function onInteract(mixed ...$args): bool { return false; }
    public function onAttack(mixed ...$args): bool { return false; }
    public function getFrictionFactor(): float { return 0.6; }
    public function getLightLevel(): int { return 0; }
    public function getLightFilter(): int { return $this->isTransparent() ? 0 : 15; }
    public function blocksDirectSkyLight(): bool { return !$this->isTransparent(); }
    public function isTransparent(): bool { return $this->typeId === 'minecraft:air'; }
    public function isSolid(): bool { return $this->typeId !== 'minecraft:air'; }
    public function canBeFlowedInto(): bool { return $this->typeId === 'minecraft:air'; }
    public function canClimb(): bool { return false; }
    /** @return Item[] */
    public function getDrops(Item $item): array { return $this->typeId === 'minecraft:air' ? [] : [$this->asItem()]; }
    /** @return Item[] */
    public function getDropsForCompatibleTool(Item $item): array { return $this->getDrops($item); }
    /** @return Item[] */
    public function getDropsForIncompatibleTool(Item $item): array { return []; }
    /** @return Item[] */
    public function getSilkTouchDrops(Item $item): array { return $this->getDrops($item); }
    public function getXpDropForTool(Item $item): int { return 0; }
    public function isAffectedBySilkTouch(): bool { return false; }
    public function getPickedItem(bool $addUserData = false): Item { return $this->asItem(); }
    public function getFuelTime(): int { return 0; }
    public function getMaxStackSize(): int { return 64; }
    public function isFireProofAsItem(): bool { return false; }
    public function getFlameEncouragement(): int { return 0; }
    public function getFlammability(): int { return 0; }
    public function burnsForever(): bool { return false; }
    public function isFlammable(): bool { return false; }
    public function onIncinerate(): void {}
    public function getSide(int $side, int $step = 1): self
    {
        $base = $this->position ?? Vector3::zero();
        $sidePos = $base->getSide($side, $step);
        return new self($this->typeId, $this->name, $this->position instanceof Position ? Position::fromObject($sidePos, $this->position->getWorld()) : $sidePos);
    }
    public function getHorizontalSides(): \Generator
    {
        foreach ([2, 3, 4, 5] as $side) {
            yield $this->getSide($side);
        }
    }
    public function getAllSides(): \Generator
    {
        for ($side = 0; $side <= 5; $side++) {
            yield $this->getSide($side);
        }
    }
    /** @return Block[] */
    public function getAffectedBlocks(): array { return [$this]; }
    public function collidesWithBB(mixed ...$args): bool { return $this->isSolid(); }
    public function hasEntityCollision(): bool { return false; }
    public function onEntityInside(mixed ...$args): bool { return false; }
    public function addVelocityToEntity(mixed ...$args): ?Vector3 { return null; }
    public function onEntityLand(mixed ...$args): ?float { return null; }
    public function onProjectileHit(mixed ...$args): void {}
    public function getCollisionBoxes(): array { return []; }
    public function getModelPositionOffset(): ?Vector3 { return null; }
    public function getSupportType(mixed ...$args): mixed { return null; }
    public function isFullCube(): bool { return $this->isSolid(); }
    public function calculateIntercept(mixed ...$args): mixed { return null; }

    /** @param list<mixed> $args */
    protected function compatMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'asItem' => $this->compatAsItem(),
            'describeBlockItemState',
            'describeBlockOnlyState',
            'writeStateToWorld',
            'onNearbyBlockChange',
            'onRandomTick',
            'onScheduledUpdate',
            'onPostPlace',
            'onProjectileHit',
            'onIncinerate' => null,
            'readStateFromWorld' => $this,
            'generateStatePermutations' => $this->singlePermutation(),
            'canBePlaced' => $this->typeId !== 'minecraft:air',
            'canBeReplaced',
            'canBeFlowedInto' => $this->typeId === 'minecraft:air',
            'canBePlacedAt',
            'place',
            'onBreak' => $this->typeId !== 'minecraft:air',
            'onInteract',
            'onAttack',
            'ticksRandomly',
            'isAffectedBySilkTouch',
            'blocksDirectSkyLight',
            'isTransparent',
            'canClimb',
            'hasEntityCollision',
            'collidesWithBB',
            'isFullCube',
            'burnsForever',
            'isFlammable' => (bool) ($this->compatState[lcfirst($method)] ?? match ($method) {
                'blocksDirectSkyLight' => !$this->isTransparent(),
                'isTransparent' => $this->typeId === 'minecraft:air',
                'isFullCube',
                'collidesWithBB' => $this->isSolid(),
                default => false,
            }),
            'isSolid' => $this->typeId !== 'minecraft:air',
            'getBreakInfo',
            'getEnchantmentTags',
            'getTypeTags',
            'getCollisionBoxes',
            'getAffectedBlocks' => match ($method) {
                'getAffectedBlocks' => [$this],
                default => [],
            },
            'getDrops',
            'getDropsForCompatibleTool',
            'getSilkTouchDrops' => $this->typeId === 'minecraft:air' ? [] : [$this->compatAsItem()],
            'getDropsForIncompatibleTool' => [],
            'getPickedItem' => $this->compatAsItem(),
            'getXpDropForTool',
            'getFuelTime',
            'getFlameEncouragement',
            'getFlammability',
            'getLightLevel' => (int) ($this->compatState[lcfirst(substr($method, 3))] ?? 0),
            'getMaxStackSize' => 64,
            'getFrictionFactor' => 0.6,
            'getLightFilter' => $this->isTransparent() ? 0 : 15,
            'isFireProofAsItem' => false,
            'addVelocityToEntity',
            'onEntityLand',
            'getModelPositionOffset',
            'getSupportType',
            'calculateIntercept' => null,
            'onEntityInside' => false,
            default => $this->compatFallback($method, $args),
        };
    }

    /** @param list<mixed> $args */
    protected static function compatStaticMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'newId' => self::$nextCompatTypeId++,
            'instant' => new BlockBreakInfo(0.0),
            'indestructible' => new BlockBreakInfo(-1.0),
            'breaksInstantly' => true,
            'axe',
            'pickaxe',
            'shovel',
            'tier' => 0,
            default => null,
        };
    }

    private function singlePermutation(): \Generator
    {
        yield clone $this;
    }

    private function compatAsItem(): Item
    {
        return match ($this->typeId) {
            'minecraft:stone' => VanillaItems::STONE(),
            'minecraft:dirt' => VanillaItems::DIRT(),
            default => VanillaItems::AIR(),
        };
    }

    /** @param list<mixed> $args */
    private function compatFallback(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'set')) {
            $this->compatState[lcfirst(substr($method, 3))] = $args[0] ?? true;
            return $this;
        }
        if (str_starts_with($method, 'get')) {
            $key = lcfirst(substr($method, 3));
            return $this->compatState[$key] ?? $this->compatDefaultFor($method);
        }
        if (str_starts_with($method, 'is') || str_starts_with($method, 'has') || str_starts_with($method, 'can')) {
            $key = lcfirst(preg_replace('/^(is|has|can)/', '', $method) ?? $method);
            return (bool) ($this->compatState[$key] ?? false);
        }
        return null;
    }

    private function compatDefaultFor(string $method): mixed
    {
        return match ($method) {
            'getCount',
            'getFillLevel',
            'getStage',
            'getDamage',
            'getFacing',
            'getAxis',
            'getSlot',
            'getSlots' => 0,
            'getFallDamagePerBlock',
            'getMaxFallDamage',
            'getBlastResistance',
            'getBreakTime',
            'getHardness' => 0.0,
            'getEmptySound',
            'getFillSound',
            'getBucketEmptySound',
            'getBucketFillSound',
            'getLandSound' => null,
            default => null,
        };
    }
}
