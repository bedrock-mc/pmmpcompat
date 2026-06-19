<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;

final class PressurePlateUpdateEvent extends BaseBlockChangeEvent
{
    /** @param \pocketmine\entity\Entity[] $activatingEntities */
    public function __construct(
        Block $block,
        Block $newState,
        private array $activatingEntities,
    ) {
        parent::__construct($block, $newState);
    }

    /** @return \pocketmine\entity\Entity[] */
    public function getActivatingEntities(): array
    {
        return $this->activatingEntities;
    }
}
