<?php

declare(strict_types=1);

namespace pocketmine\entity;

interface Consumable
{
    /**
     * @return effect\EffectInstance[]
     */
    public function getAdditionalEffects(): array;

    public function onConsume(Living $consumer): void;
}
