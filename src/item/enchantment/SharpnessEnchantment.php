<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

class SharpnessEnchantment extends MeleeWeaponEnchantment
{
    public function isApplicableTo(mixed $victim): bool { return true; }
    public function getDamageBonus(int $enchantmentLevel): float { return 0.5 * ($enchantmentLevel + 1); }
}
