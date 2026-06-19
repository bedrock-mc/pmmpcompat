<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class LevitationEffect extends Effect
{
    public function canTick(EffectInstance $instance): bool { return true; }
    public function add(mixed ...$args): void { parent::add(...$args); }
    public function applyEffect(mixed ...$args): void { parent::applyEffect(...$args); }
    public function remove(mixed ...$args): void { parent::remove(...$args); }
}
