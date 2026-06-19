<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\lang\Translatable;

class ProtectionEnchantment extends Enchantment
{
    /** @var array<int, true>|null */
    private ?array $applicableDamageTypes;

    /** @param int[]|null $applicableDamageTypes */
    public function __construct(
        Translatable|string $name,
        int $rarity,
        int $primaryItemFlags,
        int $secondaryItemFlags,
        int $maxLevel,
        private float $typeModifier,
        ?array $applicableDamageTypes,
        ?\Closure $minEnchantingPower = null,
        int $enchantingPowerRange = 50,
    ) {
        parent::__construct($name, $rarity, $primaryItemFlags, $secondaryItemFlags, $maxLevel, $minEnchantingPower, $enchantingPowerRange);
        $this->applicableDamageTypes = $applicableDamageTypes === null ? null : array_fill_keys($applicableDamageTypes, true);
    }

    public function getTypeModifier(): float { return $this->typeModifier; }
    public function getProtectionFactor(int $level): int { return (int) floor((6 + $level ** 2) * $this->typeModifier / 3); }
    public function isApplicable(EntityDamageEvent $event): bool { return $this->applicableDamageTypes === null || isset($this->applicableDamageTypes[$event->getCause()]); }
}
