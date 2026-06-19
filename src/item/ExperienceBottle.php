<?php

declare(strict_types=1);

namespace pocketmine\item;

class ExperienceBottle extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:experiencebottle', 'ExperienceBottle'); }
    public function getThrowForce(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
