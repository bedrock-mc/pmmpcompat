<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

class InstantHealthEffect extends InstantEffect
{
    public function applyEffect(mixed ...$args): void { parent::applyEffect(...$args); }
}
