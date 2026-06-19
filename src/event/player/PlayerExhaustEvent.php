<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;
use pocketmine\player\Player;

class PlayerExhaustEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public const CAUSE_ATTACK = 1;
    public const CAUSE_DAMAGE = 2;
    public const CAUSE_MINING = 3;
    public const CAUSE_HEALTH_REGEN = 4;
    public const CAUSE_POTION = 5;
    public const CAUSE_WALKING = 6;
    public const CAUSE_SPRINTING = 7;
    public const CAUSE_SWIMMING = 8;
    public const CAUSE_JUMPING = 9;
    public const CAUSE_SPRINT_JUMPING = 10;
    public const CAUSE_CUSTOM = 11;

    public function __construct(private Player $human, private float $amount, private int $cause)
    {
        parent::__construct($human);
    }

    public function getPlayer(): Player { return $this->human; }
    public function getAmount(): float { return $this->amount; }
    public function setAmount(float $amount): void { $this->amount = $amount; }
    public function getCause(): int { return $this->cause; }
}
