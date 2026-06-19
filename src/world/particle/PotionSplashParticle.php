<?php

declare(strict_types=1);

namespace pocketmine\world\particle;

class PotionSplashParticle extends SimpleParticle
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }

    public function encode(mixed ...$args): array
    {
        return parent::encode(...$args);
    }

    public static function DEFAULT_COLOR(mixed ...$args): mixed { return new \pocketmine\color\Color(0x38, 0x5d, 0xc6); }

    public function getColor(mixed ...$args): mixed { return $this->constructorArg(0, self::DEFAULT_COLOR()); }
}
