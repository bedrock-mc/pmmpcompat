<?php

declare(strict_types=1);

namespace pocketmine\block;

class Air extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:air', 'Air'); }
    public function canBePlaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function canBeReplaced(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
