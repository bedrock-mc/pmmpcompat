<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface CoralMaterial
{
    public function getCoralType(mixed ...$args): mixed;
    public function isDead(mixed ...$args): mixed;
    public function setCoralType(mixed ...$args): mixed;
    public function setDead(mixed ...$args): mixed;
}
