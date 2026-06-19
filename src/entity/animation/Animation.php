<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

interface Animation
{
    public function encode(mixed ...$args): array;
}
