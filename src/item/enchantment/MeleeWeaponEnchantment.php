<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

abstract class MeleeWeaponEnchantment extends Enchantment
{
    abstract public function isApplicableTo(mixed $victim): bool;
    abstract public function getDamageBonus(int $enchantmentLevel): float;
    public function onPostAttack(mixed $attacker, mixed $victim, int $enchantmentLevel): void {}
}
