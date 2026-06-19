<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

use pocketmine\item\Item;
use pocketmine\utils\Limits;

final class EnchantingHelper
{
    private const OPTION_NAMES = ['the elder', 'fire', 'water', 'earth', 'wind', 'light', 'dark'];

    private function __construct() {}

    public static function generateSeed(): int
    {
        return random_int(Limits::INT32_MIN, Limits::INT32_MAX);
    }

    /** @param EnchantmentInstance[] $enchantments */
    public static function enchantItem(Item $item, array $enchantments): Item
    {
        $result = clone $item;
        foreach ($enchantments as $enchantment) {
            $result->addEnchantment($enchantment);
        }
        return $result;
    }

    /** @return EnchantingOption[] */
    public static function generateOptions(mixed $tablePos, Item $input, int $seed): array
    {
        if ($input->isNull() || $input->hasEnchantments()) {
            return [];
        }

        mt_srand($seed);
        $registry = AvailableEnchantmentRegistry::getInstance();
        $available = $registry->getPrimaryEnchantmentsForItem($input);
        if ($available === []) {
            $available = $registry->getSecondaryEnchantmentsForItem($input);
        }

        $options = [];
        for ($i = 0; $i < 3; $i++) {
            $level = max(1, min(30, (int) floor(($i + 1) * 5 + mt_rand(0, 10))));
            $instances = [];
            if ($available !== []) {
                $enchantment = $available[array_rand($available)];
                $instances[] = new EnchantmentInstance($enchantment, min($enchantment->getMaxLevel(), max(1, (int) ceil($level / 10))));
            }
            $options[] = new EnchantingOption($level, self::OPTION_NAMES[array_rand(self::OPTION_NAMES)], $instances);
        }
        mt_srand();
        return $options;
    }
}
