<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use pocketmine\utils\Utils;
use pocketmine\world\Explosion;

class BlockPreExplodeEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    private bool $blockBreaking = true;

    public function __construct(
        Block $block,
        private float $radius,
        private readonly ?Player $player = null,
        private float $fireChance = 0.0,
    ) {
        parent::__construct($block);
        $this->setRadius($radius);
        $this->setFireChance($fireChance);
    }

    public function getRadius(): float
    {
        return $this->radius;
    }

    public function setRadius(float $radius): void
    {
        Utils::checkFloatNotInfOrNaN('radius', $radius);
        if ($radius <= 0) {
            throw new \InvalidArgumentException('Explosion radius must be positive');
        }
        $this->radius = $radius;
    }

    public function isBlockBreaking(): bool
    {
        return $this->blockBreaking;
    }

    public function setBlockBreaking(bool $affectsBlocks): void
    {
        $this->blockBreaking = $affectsBlocks;
    }

    public function isIncendiary(): bool
    {
        return $this->fireChance > 0;
    }

    public function setIncendiary(bool $incendiary): void
    {
        if (!$incendiary) {
            $this->fireChance = 0.0;
        } elseif ($this->fireChance <= 0.0) {
            $this->fireChance = Explosion::DEFAULT_FIRE_CHANCE;
        }
    }

    public function getFireChance(): float
    {
        return $this->fireChance;
    }

    public function setFireChance(float $fireChance): void
    {
        Utils::checkFloatNotInfOrNaN('fireChance', $fireChance);
        if ($fireChance < 0.0 || $fireChance > 1.0) {
            throw new \InvalidArgumentException('Fire chance must be a number between 0 and 1.');
        }
        $this->fireChance = $fireChance;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }
}
