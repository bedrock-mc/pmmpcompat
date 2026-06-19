<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\utils\Utils;
use pocketmine\world\Position;

class BlockExplodeEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    /** @param Block[] $blocks @param Block[] $ignitions */
    public function __construct(
        Block $block,
        private Position $position,
        private array $blocks,
        private float $yield,
        private array $ignitions,
    ) {
        parent::__construct($block);
        $this->validateYield($yield);
        $this->setAffectedBlocks($blocks);
        $this->setIgnitions($ignitions);
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getYield(): float
    {
        return $this->yield;
    }

    public function setYield(float $yield): void
    {
        $this->validateYield($yield);
        $this->yield = $yield;
    }

    /** @return Block[] */
    public function getAffectedBlocks(): array
    {
        return $this->blocks;
    }

    /** @param Block[] $blocks */
    public function setAffectedBlocks(array $blocks): void
    {
        Utils::validateArrayValueType($blocks, fn(Block $block) => null);
        $this->blocks = $blocks;
    }

    /** @return Block[] */
    public function getIgnitions(): array
    {
        return $this->ignitions;
    }

    /** @param Block[] $ignitions */
    public function setIgnitions(array $ignitions): void
    {
        Utils::validateArrayValueType($ignitions, fn(Block $block) => null);
        $this->ignitions = $ignitions;
    }

    private function validateYield(float $yield): void
    {
        Utils::checkFloatNotInfOrNaN('yield', $yield);
        if ($yield < 0.0 || $yield > 100.0) {
            throw new \InvalidArgumentException('Yield must be in range 0.0 - 100.0');
        }
    }
}
