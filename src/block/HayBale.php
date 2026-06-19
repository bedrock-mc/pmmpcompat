<?php

declare(strict_types=1);

namespace pocketmine\block;

class HayBale extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:haybale', 'HayBale'); }
    public function getFlameEncouragement(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityLand(mixed ...$args): ?float { return $this->compatMethod(__FUNCTION__, $args); }
}
