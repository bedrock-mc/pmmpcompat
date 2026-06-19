<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\nbt\tag\CompoundTag;

class Human extends Living
{
    private HungerManager $hungerManager;
    private ExperienceManager $xpManager;
    private int $enchantmentSeed;
    private ?Skin $skin;
    private string $uniqueId;

    public function __construct(?Location $location = null, ?Skin $skin = null, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        $this->skin = $skin;
        $this->hungerManager = new HungerManager($this);
        $this->xpManager = new ExperienceManager($this);
        $this->enchantmentSeed = random_int(0, 0x7fffffff);
        $this->uniqueId = bin2hex(random_bytes(16));
    }

    public function applyDamageModifiers(mixed ...$args): mixed { return null; }
    public function canEat(mixed ...$args): bool { return (bool) ($args[0] ?? false) || $this->hungerManager->isHungry(); }
    public function consumeObject(mixed ...$args): mixed { return null; }
    public function emote(mixed ...$args): mixed { return null; }
    public function getDrops(mixed ...$args): array { return []; }
    public function getEnchantmentSeed(mixed ...$args): mixed { return $this->enchantmentSeed; }
    public function getEnderInventory(mixed ...$args): mixed { return null; }
    public function getHungerManager(mixed ...$args): mixed { return $this->hungerManager; }
    public function getInventory(mixed ...$args): mixed { return null; }
    public function getName(mixed ...$args): string { return 'Human'; }
    public static function getNetworkTypeId(mixed ...$args): mixed { return 'minecraft:player'; }
    public function getOffHandInventory(mixed ...$args): mixed { return null; }
    public function getOffsetPosition(mixed ...$args): mixed { return $this->getPosition(); }
    public function getSkin(mixed ...$args): mixed { return $this->skin; }
    public function getSneakOffset(mixed ...$args): float { return 0.0; }
    public function getUniqueId(mixed ...$args): mixed { return $this->uniqueId; }
    public function getXpDropAmount(mixed ...$args): int { return min(100, $this->xpManager->getCurrentTotalXp()); }
    public function getXpManager(mixed ...$args): mixed { return $this->xpManager; }
    public function jump(mixed ...$args): mixed { return null; }
    public static function parseSkinNBT(mixed ...$args): mixed { return null; }
    public function regenerateEnchantmentSeed(mixed ...$args): void { $this->enchantmentSeed = random_int(0, 0x7fffffff); }
    public function saveNBT(mixed ...$args): mixed { return parent::saveNBT(); }
    public function sendSkin(mixed ...$args): mixed { return null; }
    public function setEnchantmentSeed(mixed ...$args): void { $this->enchantmentSeed = (int) ($args[0] ?? $this->enchantmentSeed); }
    public function setSkin(mixed ...$args): void { $this->skin = $args[0] instanceof Skin ? $args[0] : null; }
    public function spawnTo(mixed ...$args): mixed { return null; }
}
