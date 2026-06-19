<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class RegenerationEffect extends Effect
{
    public function canTick(EffectInstance $instance): bool { return $instance->getDuration() % max(1, 50 >> $instance->getAmplifier()) === 0; }
    public function applyEffect(mixed ...$args): void { parent::applyEffect(...$args); }
}
