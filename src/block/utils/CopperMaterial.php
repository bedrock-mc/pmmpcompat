<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface CopperMaterial
{
    public function getOxidation(mixed ...$args): mixed;
    public function isWaxed(mixed ...$args): mixed;
    public function setOxidation(mixed ...$args): mixed;
    public function setWaxed(mixed ...$args): mixed;
}
