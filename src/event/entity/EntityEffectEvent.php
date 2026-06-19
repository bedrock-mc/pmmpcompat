<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityEffectEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Entity $entity,
        private EffectInstance $effect,
    ) {
        $this->entity = $entity;
    }

    public function getEffect(): EffectInstance
    {
        return $this->effect;
    }
}
