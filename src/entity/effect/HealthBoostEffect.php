<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class HealthBoostEffect extends Effect
{
    public function add(mixed ...$args): void { parent::add(...$args); }
    public function remove(mixed ...$args): void { parent::remove(...$args); }
}
