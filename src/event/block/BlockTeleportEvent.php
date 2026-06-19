<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;
use pocketmine\utils\Utils;

class BlockTeleportEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        Block $block,
        private Vector3 $to,
    ) {
        parent::__construct($block);
    }

    public function getTo(): Vector3
    {
        return $this->to;
    }

    public function setTo(Vector3 $to): void
    {
        Utils::checkVector3NotInfOrNaN($to);
        $this->to = $to;
    }
}
