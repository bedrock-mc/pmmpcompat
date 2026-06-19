<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

class FireAspectEnchantment extends MeleeWeaponEnchantment
{
    public function isApplicableTo(mixed $victim): bool { return true; }
    public function getDamageBonus(int $enchantmentLevel): float { return 0.0; }
    public function onPostAttack(mixed $attacker, mixed $victim, int $enchantmentLevel): void
    {
        if (is_object($victim) && method_exists($victim, 'setOnFire')) {
            $victim->setOnFire($enchantmentLevel * 4);
        }
    }
}
