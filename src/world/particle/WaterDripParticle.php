<?php

declare(strict_types=1);

namespace pocketmine\world\particle;

class WaterDripParticle extends SimpleParticle
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }

    public function encode(mixed ...$args): array
    {
        return parent::encode(...$args);
    }
}
