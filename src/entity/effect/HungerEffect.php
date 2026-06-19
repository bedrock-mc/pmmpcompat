<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class HungerEffect extends Effect
{
    public function canTick(EffectInstance $instance): bool { return $instance->getDuration() % 80 === 0; }
    public function applyEffect(mixed ...$args): void { parent::applyEffect(...$args); }
}
