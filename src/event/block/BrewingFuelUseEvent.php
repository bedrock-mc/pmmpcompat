<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\tile\BrewingStand;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class BrewingFuelUseEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    private int $fuelTime = 20;

    public function __construct(private BrewingStand $brewingStand)
    {
        parent::__construct($brewingStand->getBlock());
    }

    public function getBrewingStand(): BrewingStand
    {
        return $this->brewingStand;
    }

    public function getFuelTime(): int
    {
        return $this->fuelTime;
    }

    public function setFuelTime(int $fuelTime): void
    {
        if ($fuelTime <= 0) {
            throw new \InvalidArgumentException('Fuel time must be positive');
        }
        $this->fuelTime = $fuelTime;
    }
}
