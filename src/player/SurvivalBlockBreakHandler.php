<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\block\Block;
use pocketmine\math\Vector3;

final class SurvivalBlockBreakHandler
{
    public const DEFAULT_FX_INTERVAL_TICKS = 5;

    private int $fxTicker = 0;
    private float $breakSpeed;
    private float $breakProgress = 0.0;

    public function __construct(
        private Player $player,
        private Vector3 $blockPos,
        private Block $block,
        private int $targetedFace,
        private int $maxPlayerDistance,
        private int $fxTickInterval = self::DEFAULT_FX_INTERVAL_TICKS
    ) {
        $this->setTargetedFace($targetedFace);
        $this->breakSpeed = $this->calculateBreakProgressPerTick();
        if ($this->breakSpeed > 0.0) {
            $this->player->getWorld()->broadcastPacketToViewers($this->blockPos, ['event' => 'block_start_break', 'speed' => $this->breakSpeed]);
        }
    }

    public function update(): bool
    {
        if ($this->player->getPosition()->distanceSquared($this->blockPos->add(0.5, 0.5, 0.5)) > $this->maxPlayerDistance ** 2) {
            return false;
        }

        $this->breakSpeed = $this->calculateBreakProgressPerTick();
        $this->breakProgress += $this->breakSpeed;

        if (($this->fxTicker++ % max(1, $this->fxTickInterval)) === 0 && $this->breakProgress < 1.0) {
            $this->player->getWorld()->addParticle($this->blockPos, ['event' => 'block_punch', 'block' => $this->block, 'face' => $this->targetedFace]);
            $this->player->getWorld()->addSound($this->blockPos, ['event' => 'block_punch', 'block' => $this->block]);
            $this->player->broadcastAnimation(['event' => 'arm_swing'], $this->player->getViewers());
        }

        return $this->breakProgress < 1.0;
    }

    public function getBlockPos(): Vector3
    {
        return $this->blockPos;
    }

    public function getTargetedFace(): int
    {
        return $this->targetedFace;
    }

    public function setTargetedFace(int $face): void
    {
        if ($face < 0 || $face > 5) {
            throw new \InvalidArgumentException('Invalid block face: ' . $face);
        }
        $this->targetedFace = $face;
    }

    public function getBreakSpeed(): float
    {
        return $this->breakSpeed;
    }

    public function getBreakProgress(): float
    {
        return $this->breakProgress;
    }

    public function __destruct()
    {
        if ($this->player->getWorld()->isInLoadedTerrain($this->blockPos)) {
            $this->player->getWorld()->broadcastPacketToViewers($this->blockPos, ['event' => 'block_stop_break']);
        }
    }

    private function calculateBreakProgressPerTick(): float
    {
        if (!$this->block->isSolid()) {
            return 1.0;
        }
        return 0.05;
    }
}
