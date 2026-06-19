<?php

declare(strict_types=1);

namespace pocketmine\world\particle;

interface Particle
{
    public function encode(mixed ...$args): mixed;
}
