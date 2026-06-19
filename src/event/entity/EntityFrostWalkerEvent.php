<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\block\Block;
use pocketmine\block\Liquid;
use pocketmine\entity\Living;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class EntityFrostWalkerEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Living $entity,
        private int $radius,
        private Liquid $liquid,
        private Block $targetBlock,
    ) {
        $this->entity = $entity;
    }

    public function getRadius(): int { return $this->radius; }
    public function setRadius(int $radius): void { $this->radius = $radius; }
    public function getLiquid(): Liquid { return $this->liquid; }
    public function setLiquid(Liquid $liquid): void { $this->liquid = $liquid; }
    public function getTargetBlock(): Block { return $this->targetBlock; }
    public function setTargetBlock(Block $targetBlock): void { $this->targetBlock = $targetBlock; }
}
