<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

final class IncompatibleEnchantmentRegistry
{
    private static ?self $instance = null;
    /** @var array<int, array<string, true>> */
    private array $incompatibilityMap = [];

    private function __construct()
    {
        $this->register(IncompatibleEnchantmentGroups::PROTECTION, [
            VanillaEnchantments::PROTECTION(),
            VanillaEnchantments::FIRE_PROTECTION(),
            VanillaEnchantments::BLAST_PROTECTION(),
            VanillaEnchantments::PROJECTILE_PROTECTION(),
        ]);
        $this->register(IncompatibleEnchantmentGroups::BOW_INFINITE, [VanillaEnchantments::INFINITY(), VanillaEnchantments::MENDING()]);
        $this->register(IncompatibleEnchantmentGroups::BLOCK_DROPS, [VanillaEnchantments::FORTUNE(), VanillaEnchantments::SILK_TOUCH()]);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    /** @param Enchantment[] $enchantments */
    public function register(string $tag, array $enchantments): void
    {
        foreach ($enchantments as $enchantment) {
            $this->incompatibilityMap[spl_object_id($enchantment)][$tag] = true;
        }
    }

    /** @param Enchantment[] $enchantments */
    public function unregister(string $tag, array $enchantments): void
    {
        foreach ($enchantments as $enchantment) {
            unset($this->incompatibilityMap[spl_object_id($enchantment)][$tag]);
        }
    }

    public function unregisterAll(string $tag): void
    {
        foreach ($this->incompatibilityMap as $id => $tags) {
            unset($this->incompatibilityMap[$id][$tag]);
        }
    }

    public function areCompatible(Enchantment $first, Enchantment $second): bool
    {
        $firstTags = $this->incompatibilityMap[spl_object_id($first)] ?? [];
        $secondTags = $this->incompatibilityMap[spl_object_id($second)] ?? [];
        return array_intersect_key($firstTags, $secondTags) === [];
    }
}
