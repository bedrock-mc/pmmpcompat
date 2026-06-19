<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\nbt\tag\CompoundTag;

class Villager extends Living implements Ageable
{
    public const PROFESSION_FARMER = 0;
    public const PROFESSION_LIBRARIAN = 1;
    public const PROFESSION_PRIEST = 2;
    public const PROFESSION_BLACKSMITH = 3;
    public const PROFESSION_BUTCHER = 4;

    private const TAG_PROFESSION = 'Profession';

    private bool $baby = false;
    private int $profession = self::PROFESSION_FARMER;

    public function __construct(?Location $location = null, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        if ($nbt !== null) {
            $this->setProfession($nbt->getInt(self::TAG_PROFESSION, self::PROFESSION_FARMER));
        }
    }

    public function getName(mixed ...$args): string { return 'Villager'; }
    public static function getNetworkTypeId(mixed ...$args): mixed { return 'minecraft:villager'; }
    public function getPickedItem(mixed ...$args): mixed { return null; }
    public function getProfession(mixed ...$args): int { return $this->profession; }
    public function isBaby(): bool { return $this->baby; }
    public function saveNBT(mixed ...$args): mixed { return parent::saveNBT()->setInt(self::TAG_PROFESSION, $this->profession); }
    public function setProfession(mixed ...$args): void
    {
        $profession = (int) ($args[0] ?? self::PROFESSION_FARMER);
        $this->profession = $profession >= self::PROFESSION_FARMER && $profession <= self::PROFESSION_BUTCHER ? $profession : self::PROFESSION_FARMER;
    }
}
