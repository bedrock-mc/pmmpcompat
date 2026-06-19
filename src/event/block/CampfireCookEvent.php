<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\block\Campfire;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;

class CampfireCookEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(
        private Campfire $campfire,
        private int $slot,
        private Item $input,
        private Item $result,
    ) {
        parent::__construct($campfire);
        $this->input = clone $input;
    }

    public function getCampfire(): Campfire
    {
        return $this->campfire;
    }

    public function getSlot(): int
    {
        return $this->slot;
    }

    public function getInput(): Item
    {
        return $this->input;
    }

    public function getResult(): Item
    {
        return $this->result;
    }

    public function setResult(Item $result): void
    {
        $this->result = $result;
    }
}
