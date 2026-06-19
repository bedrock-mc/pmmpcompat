<?php

declare(strict_types=1);

namespace pocketmine\item;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;

class Item implements \JsonSerializable
{
    public const TAG_BLOCK_ENTITY_TAG = 'BlockEntityTag';
    public const TAG_DISPLAY = 'display';
    public const TAG_DISPLAY_LORE = 'Lore';
    public const TAG_DISPLAY_NAME = 'Name';
    public const TAG_ENCH = 'ench';
    public const TAG_KEEP_ON_DEATH = 'minecraft:keep_on_death';

    private ?string $customName = null;
    /** @var string[] */
    private array $lore = [];
    /** @var string[] */
    private array $canDestroy = [];
    /** @var string[] */
    private array $canPlaceOn = [];
    private bool $keepOnDeath = false;
    private mixed $namedTag = null;
    private mixed $customBlockData = null;
    /** @var array<string, mixed> */
    private array $compatState = [];
    /** @var array<int|string, mixed> */
    private array $compatEnchantments = [];
    private static int $nextCompatTypeId = 100000;

    public function __construct(
        private string $typeId,
        private string $name,
        private int $count = 1,
    ) {}

    public function __clone() {}
    public function __toString(): string { return $this->name . ' (' . $this->typeId . ') x ' . $this->count; }
    public function getTypeId(): string { return $this->typeId; }
    public function getName(): string { return $this->name; }
    public function getVanillaName(): string { return $this->name; }
    public function getCount(): int { return $this->count; }

    public function setCount(int $count): self
    {
        $this->count = max(0, $count);
        return $this;
    }

    public function isNull(): bool { return $this->count <= 0 || $this->typeId === 'minecraft:air'; }
    public function getMaxStackSize(): int { return $this->typeId === 'minecraft:air' ? 0 : 64; }
    public function canStackWith(self $other): bool { return $this->equals($other, true, true); }
    public function equals(self $item, bool $checkDamage = true, bool $checkCompound = true): bool { return $this->typeId === $item->typeId; }
    public function equalsExact(self $item): bool { return $this->typeId === $item->typeId && $this->count === $item->count && $this->customName === $item->customName && $this->lore === $item->lore; }
    public function pop(int $count = 1): self
    {
        $removed = min(max(0, $count), $this->count);
        $this->count -= $removed;
        return (clone $this)->setCount($removed);
    }

    public function getBlock(): Block { return match ($this->typeId) {
        'minecraft:stone' => VanillaBlocks::STONE(),
        'minecraft:dirt' => VanillaBlocks::DIRT(),
        'minecraft:grass' => VanillaBlocks::GRASS(),
        default => VanillaBlocks::AIR(),
    }; }
    public function canBePlaced(): bool { return $this->getBlock()->getTypeId() !== 'minecraft:air'; }
    public function getAttackPoints(): int { return str_contains($this->typeId, 'sword') ? 7 : 1; }
    public function getDefensePoints(): int { return 0; }
    public function getFuelTime(): int { return 0; }
    public function getFuelResidue(): ?self { return null; }
    public function getCooldownTag(): ?string { return null; }
    public function getCooldownTicks(): int { return 0; }
    public function getEnchantability(): int { return 0; }
    /** @return string[] */
    public function getEnchantmentTags(): array { return []; }
    public function addEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantmentLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function removeEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function removeEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getBlockToolType(): int { return 0; }
    public function getBlockToolHarvestLevel(): int { return 0; }
    public function getMiningEfficiency(bool $isCorrectTool): float { return $isCorrectTool ? 1.0 : 1.0; }
    public function isFireProof(): bool { return false; }
    public function getStateId(): int { return crc32($this->typeId); }

    public function hasCustomName(): bool { return $this->customName !== null; }
    public function getCustomName(): string { return $this->customName ?? ''; }
    public function setCustomName(string $name): self { $this->customName = $name; return $this; }
    public function clearCustomName(): self { $this->customName = null; return $this; }
    /** @return string[] */
    public function getLore(): array { return $this->lore; }
    /** @param string[] $lines */
    public function setLore(array $lines): self { $this->lore = array_map('strval', $lines); return $this; }
    public function keepOnDeath(): bool { return $this->keepOnDeath; }
    public function setKeepOnDeath(bool $keepOnDeath = true): self { $this->keepOnDeath = $keepOnDeath; return $this; }

    /** @return string[] */
    public function getCanDestroy(): array { return $this->canDestroy; }
    /** @param string[] $blocks */
    public function setCanDestroy(array $blocks): self { $this->canDestroy = array_map('strval', $blocks); return $this; }
    /** @return string[] */
    public function getCanPlaceOn(): array { return $this->canPlaceOn; }
    /** @param string[] $blocks */
    public function setCanPlaceOn(array $blocks): self { $this->canPlaceOn = array_map('strval', $blocks); return $this; }

    public function hasNamedTag(): bool { return $this->namedTag !== null; }
    public function getNamedTag(): mixed { return $this->namedTag; }
    public function setNamedTag(mixed $tag): self { $this->namedTag = $tag; return $this; }
    public function clearNamedTag(): self { $this->namedTag = null; return $this; }
    public function hasCustomBlockData(): bool { return $this->customBlockData !== null; }
    public function getCustomBlockData(): mixed { return $this->customBlockData; }
    public function setCustomBlockData(mixed $data): self { $this->customBlockData = $data; return $this; }
    public function clearCustomBlockData(): self { $this->customBlockData = null; return $this; }

    public function nbtSerialize(): array { return $this->jsonSerialize(); }
    public static function nbtDeserialize(mixed $tag): self { return self::safeNbtDeserialize($tag); }
    public static function safeNbtDeserialize(mixed $tag): self
    {
        return is_array($tag) ? new self((string) ($tag['type_id'] ?? 'minecraft:air'), (string) ($tag['name'] ?? 'Air'), (int) ($tag['count'] ?? 0)) : new self('minecraft:air', 'Air', 0);
    }
    public static function legacyJsonDeserialize(array $data): self { return self::safeNbtDeserialize($data); }

    public function getPlacementTransaction(mixed ...$args): mixed { return null; }
    public function onClickAir(mixed ...$args): bool { return false; }
    public function onInteractBlock(mixed ...$args): bool { return false; }
    public function onInteractEntity(mixed ...$args): bool { return false; }
    public function onAttackEntity(mixed ...$args): bool { return false; }
    public function onDestroyBlock(mixed ...$args): bool { return false; }
    public function onReleaseUsing(mixed ...$args): bool { return false; }
    public function onTickWorn(mixed ...$args): bool { return false; }

    public function jsonSerialize(): array
    {
        return ['type_id' => $this->typeId, 'name' => $this->name, 'count' => $this->count];
    }

    /** @param list<mixed> $args */
    protected function compatMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'getFoodRestore',
            'getSaturationRestore',
            'getAdditionalEffects',
            'getEffects',
            'getExplosions',
            'getPatterns',
            'getColors',
            'getFadeColors',
            'getMappings' => [],
            'getTypeId' => $this->typeId,
            'getMaxStackSize' => $this->typeId === 'minecraft:air' ? 0 : 64,
            'getBlock' => match ($this->typeId) {
                'minecraft:stone' => VanillaBlocks::STONE(),
                'minecraft:dirt' => VanillaBlocks::DIRT(),
                'minecraft:grass' => VanillaBlocks::GRASS(),
                default => VanillaBlocks::AIR(),
            },
            'getFuelTime' => 0,
            'getFuelResidue' => null,
            'getAttackPoints' => str_contains($this->typeId, 'sword') ? 7 : 1,
            'getDefensePoints' => 0,
            'getBlockToolType' => 0,
            'getBlockToolHarvestLevel',
            'getHarvestLevel' => 0,
            'getMiningEfficiency' => 1.0,
            'getEnchantability' => 0,
            'isFireProof' => false,
            'getPlacementTransaction' => null,
            'isNull' => $this->count <= 0 || $this->typeId === 'minecraft:air',
            'onClickAir',
            'onInteractBlock',
            'onInteractEntity',
            'onAttackEntity',
            'onDestroyBlock',
            'onReleaseUsing',
            'onTickWorn' => false,
            'getMaxDurability' => (int) ($this->compatState['maxDurability'] ?? 0),
            'getDamage' => (int) ($this->compatState['damage'] ?? 0),
            'isBroken' => ((int) ($this->compatState['maxDurability'] ?? 0)) > 0 && ((int) ($this->compatState['damage'] ?? 0)) >= ((int) $this->compatState['maxDurability']),
            'isUnbreakable' => (bool) ($this->compatState['unbreakable'] ?? false),
            'applyDamage' => $this->compatApplyDamage((int) ($args[0] ?? 1)),
            'setDamage' => $this->compatSet('damage', max(0, (int) ($args[0] ?? 0))),
            'setUnbreakable' => $this->compatSet('unbreakable', (bool) ($args[0] ?? true)),
            'addEnchantment' => $this->compatAddEnchantment($args[0] ?? null),
            'getEnchantments' => array_values($this->compatEnchantments),
            'hasEnchantments' => count($this->compatEnchantments) > 0,
            'removeEnchantments' => $this->compatClearEnchantments(),
            'getEnchantment' => $this->compatEnchantments[$this->compatKey($args[0] ?? '')] ?? null,
            'getEnchantmentLevel' => $this->compatEnchantmentLevel($args[0] ?? ''),
            'hasEnchantment' => isset($this->compatEnchantments[$this->compatKey($args[0] ?? '')]),
            'removeEnchantment' => $this->compatRemoveEnchantment($args[0] ?? ''),
            default => $this->compatFallback($method, $args),
        };
    }

    /** @param list<mixed> $args */
    protected static function compatStaticMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'newId' => self::$nextCompatTypeId++,
            'fromBlock' => $args[0] instanceof Block ? new self($args[0]->getTypeId(), $args[0]->getName()) : new self('minecraft:air', 'Air', 0),
            'fromBlockTypeId' => new self('minecraft:block_' . (string) ($args[0] ?? 0), 'Block ' . (string) ($args[0] ?? 0)),
            'toBlockTypeId' => (int) ($args[0] ?? 0),
            'fromCompoundTag' => self::safeNbtDeserialize(is_array($args[0] ?? null) ? $args[0] : []),
            'getAll',
            'lookupAliases',
            'lookupBlockAliases',
            'getMappings' => [],
            'parse' => null,
            default => null,
        };
    }

    private function compatFallback(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'set')) {
            return $this->compatSet(lcfirst(substr($method, 3)), $args[0] ?? true);
        }
        if (str_starts_with($method, 'clear')) {
            unset($this->compatState[lcfirst(substr($method, 5))]);
            return $this;
        }
        if (str_starts_with($method, 'get')) {
            $key = lcfirst(substr($method, 3));
            return $this->compatState[$key] ?? $this->compatDefaultFor($method);
        }
        if (str_starts_with($method, 'is') || str_starts_with($method, 'has') || str_starts_with($method, 'can') || str_starts_with($method, 'requires') || str_starts_with($method, 'will')) {
            $key = lcfirst(preg_replace('/^(is|has|can|requires|will)/', '', $method) ?? $method);
            return (bool) ($this->compatState[$key] ?? false);
        }
        return null;
    }

    private function compatDefaultFor(string $method): mixed
    {
        return match ($method) {
            'getThrowForce' => 1.5,
            'getDisplayName' => $this->getName(),
            'getColor',
            'getCustomColor',
            'getFlashColor',
            'getTrail',
            'getExplosion',
            'getExplosionSound',
            'getEquipSound',
            'getLiquid',
            'getResidue',
            'getRecordType',
            'getWoodType',
            'getType',
            'getTier',
            'getMaterial',
            'getArmorSlot',
            'getHornType',
            'getCuredEffect' => null,
            default => 0,
        };
    }

    private function compatApplyDamage(int $amount): bool
    {
        if ($this->compatMethod('isUnbreakable', [])) {
            return false;
        }
        $this->compatState['damage'] = max(0, ((int) ($this->compatState['damage'] ?? 0)) + max(0, $amount));
        return $this->compatMethod('isBroken', []);
    }

    private function compatSet(string $key, mixed $value): self
    {
        $this->compatState[$key] = $value;
        return $this;
    }

    private function compatAddEnchantment(mixed $enchantment): self
    {
        if ($enchantment !== null) {
            $this->compatEnchantments[$this->compatKey($enchantment)] = $enchantment;
        }
        return $this;
    }

    private function compatRemoveEnchantment(mixed $enchantment): self
    {
        unset($this->compatEnchantments[$this->compatKey($enchantment)]);
        return $this;
    }

    private function compatClearEnchantments(): self
    {
        $this->compatEnchantments = [];
        return $this;
    }

    private function compatEnchantmentLevel(mixed $enchantment): int
    {
        $stored = $this->compatEnchantments[$this->compatKey($enchantment)] ?? null;
        return is_object($stored) && method_exists($stored, 'getLevel') ? (int) $stored->getLevel() : ($stored === null ? 0 : 1);
    }

    private function compatKey(mixed $value): int|string
    {
        if (is_object($value)) {
            return method_exists($value, 'getTypeId') ? (string) $value->getTypeId() : spl_object_id($value);
        }
        return is_int($value) || is_string($value) ? $value : serialize($value);
    }
}
