<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

final class VanillaEnchantments
{
    /** @var array<string, Enchantment>|null */
    private static ?array $all = null;

    private function __construct() {}

    /** @return array<string, Enchantment> */
    public static function getAll(): array
    {
        return self::$all ??= [
            'PROTECTION' => new Enchantment('enchantment.protect.all', Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4),
            'FIRE_PROTECTION' => new Enchantment('enchantment.protect.fire', Rarity::UNCOMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4),
            'FEATHER_FALLING' => new Enchantment('enchantment.protect.fall', Rarity::UNCOMMON, ItemFlags::FEET, ItemFlags::NONE, 4),
            'BLAST_PROTECTION' => new Enchantment('enchantment.protect.explosion', Rarity::RARE, ItemFlags::ARMOR, ItemFlags::NONE, 4),
            'PROJECTILE_PROTECTION' => new Enchantment('enchantment.protect.projectile', Rarity::UNCOMMON, ItemFlags::ARMOR, ItemFlags::NONE, 4),
            'THORNS' => new Enchantment('enchantment.thorns', Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::NONE, 3),
            'RESPIRATION' => new Enchantment('enchantment.oxygen', Rarity::RARE, ItemFlags::HEAD, ItemFlags::NONE, 3),
            'FROST_WALKER' => new Enchantment('enchantment.frostwalker', Rarity::RARE, ItemFlags::FEET, ItemFlags::NONE, 2),
            'AQUA_AFFINITY' => new Enchantment('enchantment.waterWorker', Rarity::RARE, ItemFlags::HEAD, ItemFlags::NONE, 1),
            'SHARPNESS' => new SharpnessEnchantment('enchantment.damage.all', Rarity::COMMON, ItemFlags::SWORD | ItemFlags::AXE, ItemFlags::NONE, 5),
            'KNOCKBACK' => new KnockbackEnchantment('enchantment.knockback', Rarity::UNCOMMON, ItemFlags::SWORD, ItemFlags::NONE, 2),
            'FIRE_ASPECT' => new FireAspectEnchantment('enchantment.fire', Rarity::RARE, ItemFlags::SWORD, ItemFlags::NONE, 2),
            'EFFICIENCY' => new Enchantment('enchantment.digging', Rarity::COMMON, ItemFlags::DIG, ItemFlags::NONE, 5),
            'SILK_TOUCH' => new Enchantment('enchantment.untouching', Rarity::MYTHIC, ItemFlags::DIG, ItemFlags::NONE, 1),
            'UNBREAKING' => new Enchantment('enchantment.durability', Rarity::UNCOMMON, ItemFlags::ALL, ItemFlags::NONE, 3),
            'FORTUNE' => new Enchantment('enchantment.lootBonusDigger', Rarity::RARE, ItemFlags::DIG, ItemFlags::NONE, 3),
            'POWER' => new Enchantment('enchantment.arrowDamage', Rarity::COMMON, ItemFlags::BOW, ItemFlags::NONE, 5),
            'PUNCH' => new Enchantment('enchantment.arrowKnockback', Rarity::RARE, ItemFlags::BOW, ItemFlags::NONE, 2),
            'FLAME' => new Enchantment('enchantment.arrowFire', Rarity::RARE, ItemFlags::BOW, ItemFlags::NONE, 1),
            'INFINITY' => new Enchantment('enchantment.arrowInfinite', Rarity::MYTHIC, ItemFlags::BOW, ItemFlags::NONE, 1),
            'MENDING' => new Enchantment('enchantment.mending', Rarity::RARE, ItemFlags::ALL, ItemFlags::NONE, 1),
            'VANISHING' => new Enchantment('enchantment.curse.vanishing', Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::NONE, 1),
            'SWIFT_SNEAK' => new Enchantment('enchantment.swift_sneak', Rarity::RARE, ItemFlags::LEGS, ItemFlags::NONE, 3),
        ];
    }

    public static function __callStatic(string $name, array $arguments): Enchantment
    {
        $all = self::getAll();
        $key = strtoupper($name);
        if (!isset($all[$key])) {
            throw new \BadMethodCallException('Unknown vanilla enchantment ' . $name);
        }
        return $all[$key];
    }
}
