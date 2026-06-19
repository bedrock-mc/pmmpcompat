<?php

declare(strict_types=1);

namespace pocketmine\item;

trait ItemEnchantmentHandlingTrait
{
    public function addEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantmentLevel(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function removeEnchantment(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function removeEnchantments(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
