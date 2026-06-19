<?php

declare(strict_types=1);

namespace pocketmine\entity\projectile;

use pocketmine\entity\EntitySizeInfo;

abstract class Throwable extends Projectile
{
    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.25, 0.25);
    }

    protected function getInitialDragMultiplier(): float
    {
        return 0.01;
    }

    protected function getInitialGravity(): float
    {
        return 0.03;
    }
}
