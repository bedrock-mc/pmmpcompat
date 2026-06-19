<?php

declare(strict_types=1);

namespace pocketmine\block;

class BoneBlock extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:boneblock', 'BoneBlock'); }
}
