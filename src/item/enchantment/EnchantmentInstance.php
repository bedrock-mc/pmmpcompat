<?php

declare(strict_types=1);

namespace pocketmine\item\enchantment;

final class EnchantmentInstance
{
    public function __construct(private Enchantment $enchantment, private int $level = 1) {}
    public function getType(): Enchantment { return $this->enchantment; }
    public function getLevel(): int { return $this->level; }
}
