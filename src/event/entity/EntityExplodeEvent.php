<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\utils\Utils;
use pocketmine\world\Position;

class EntityExplodeEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    /** @param Block[] $blocks @param Block[] $ignitions */
    public function __construct(
        Entity $entity,
        protected Position $position,
        protected array $blocks,
        protected float $yield,
        private array $ignitions = [],
    ) {
        $this->entity = $entity;
        $this->setYield($yield);
        $this->setBlockList($blocks);
        $this->setIgnitions($ignitions);
    }

    public function getPosition(): Position { return $this->position; }
    /** @return Block[] */
    public function getBlockList(): array { return $this->blocks; }
    /** @param Block[] $blocks */
    public function setBlockList(array $blocks): void
    {
        Utils::validateArrayValueType($blocks, fn(Block $_) => null);
        $this->blocks = $blocks;
    }
    public function getYield(): float { return $this->yield; }
    public function setYield(float $yield): void
    {
        if ($yield < 0.0 || $yield > 100.0) {
            throw new \InvalidArgumentException('Yield must be in range 0.0 - 100.0');
        }
        $this->yield = $yield;
    }
    /** @return Block[] */
    public function getIgnitions(): array { return $this->ignitions; }
    /** @param Block[] $ignitions */
    public function setIgnitions(array $ignitions): void
    {
        Utils::validateArrayValueType($ignitions, fn(Block $block) => null);
        $this->ignitions = $ignitions;
    }
}
