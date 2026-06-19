<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class InstantEffect extends Effect
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function canTick(EffectInstance $instance): bool { return true; }
}
