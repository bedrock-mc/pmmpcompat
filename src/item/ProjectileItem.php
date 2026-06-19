<?php

declare(strict_types=1);

namespace pocketmine\item;

class ProjectileItem extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:projectileitem', 'ProjectileItem'); }
    public function getThrowForce(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onClickAir(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
