<?php

declare(strict_types=1);

namespace pocketmine\block;

class BaseCake extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:basecake', 'BaseCake'); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getFoodRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSaturationRestore(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onConsume(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function requiresHunger(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
