<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityRegainHealthEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public const CAUSE_REGEN = 0;
    public const CAUSE_EATING = 1;
    public const CAUSE_MAGIC = 2;
    public const CAUSE_CUSTOM = 3;
    public const CAUSE_SATURATION = 4;

    public function __construct(
        Entity $entity,
        private float $amount,
        private int $regainReason,
    ) {
        $this->entity = $entity;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getRegainReason(): int
    {
        return $this->regainReason;
    }
}
