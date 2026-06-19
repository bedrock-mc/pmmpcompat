<?php

declare(strict_types=1);

namespace pocketmine\world\particle;

class FloatingTextParticle extends SimpleParticle
{
    public function __construct(mixed ...$args)
    {
        parent::__construct(...$args);
    }

    public function encode(mixed ...$args): array
    {
        return parent::encode(...$args);
    }

    public function getText(mixed ...$args): mixed { return (string) $this->constructorArg(0, ""); }

    public function getTitle(mixed ...$args): mixed { return (string) $this->constructorArg(1, ""); }

    public function isInvisible(mixed ...$args): mixed { return (bool) $this->constructorArg(2, false); }

    public function setInvisible(mixed ...$args): mixed { $values = $this->constructorArgs(); $values[2] = $args[0] ?? true; parent::__construct(...$values); return null; }

    public function setText(mixed ...$args): mixed { $values = $this->constructorArgs(); $values[0] = (string) ($args[0] ?? ""); parent::__construct(...$values); return null; }

    public function setTitle(mixed ...$args): mixed { $values = $this->constructorArgs(); $values[1] = (string) ($args[0] ?? ""); parent::__construct(...$values); return null; }
}
