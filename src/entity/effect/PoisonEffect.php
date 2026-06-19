<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class PoisonEffect extends Effect
{
    public function __construct(mixed ...$args) { parent::__construct(...$args); }
    public function canTick(EffectInstance $instance): bool { return $instance->getDuration() % max(1, 25 >> $instance->getAmplifier()) === 0; }
    public function applyEffect(mixed ...$args): void { parent::applyEffect(...$args); }
}
