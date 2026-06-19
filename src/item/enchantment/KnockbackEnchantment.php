<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

class KnockbackEnchantment extends MeleeWeaponEnchantment
{
    public function isApplicableTo(mixed $victim): bool { return true; }
    public function getDamageBonus(int $enchantmentLevel): float { return 0.0; }
    public function onPostAttack(mixed $attacker, mixed $victim, int $enchantmentLevel): void {}
}
