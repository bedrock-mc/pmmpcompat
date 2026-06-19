<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

/**
 * Represents an option on the enchanting table menu.
 * If selected, all enchantments in the option are applied to the item.
 */
class EnchantingOption
{
    /**
     * @param EnchantmentInstance[] $enchantments
     */
    public function __construct(
        private int $requiredXpLevel,
        private string $displayName,
        private array $enchantments
    ) {}

    public function getRequiredXpLevel(): int
    {
        return $this->requiredXpLevel;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return EnchantmentInstance[]
     */
    public function getEnchantments(): array
    {
        return $this->enchantments;
    }
}
