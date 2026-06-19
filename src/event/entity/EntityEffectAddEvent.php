<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\Entity;

class EntityEffectAddEvent extends EntityEffectEvent
{
    public function __construct(Entity $entity, EffectInstance $effect, private ?EffectInstance $oldEffect = null)
    {
        parent::__construct($entity, $effect);
    }

    public function willModify(): bool { return $this->hasOldEffect(); }
    public function hasOldEffect(): bool { return $this->oldEffect instanceof EffectInstance; }
    public function getOldEffect(): ?EffectInstance { return $this->oldEffect; }
}
