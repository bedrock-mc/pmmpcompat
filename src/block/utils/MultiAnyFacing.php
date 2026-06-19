<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

interface MultiAnyFacing
{
    public function getFaces(mixed ...$args): mixed;
    public function hasFace(mixed ...$args): mixed;
    public function setFace(mixed ...$args): mixed;
    public function setFaces(mixed ...$args): mixed;
}
