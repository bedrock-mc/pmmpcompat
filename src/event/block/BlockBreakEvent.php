<?php

declare(strict_types=1);

namespace pocketmine\event\block;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BlockBreakEvent extends Event implements Cancellable
{
    use CancellableTrait;

    /** @var Item[] */
    private array $drops = [];
    private Item $item;
    private bool $instaBreak = false;
    private int $xpDropAmount = 0;

    /** @param Item[] $drops */
    public function __construct(private Player $player, private Vector3|Block $blockPosition, ?Item $item = null, bool $instaBreak = false, array $drops = [], int $xpDrops = 0)
    {
        $this->item = $item ?? VanillaItems::AIR();
        $this->instaBreak = $instaBreak;
        $this->setDrops($drops);
        $this->xpDropAmount = max(0, $xpDrops);
    }

    public function getPlayer(): Player { return $this->player; }
    public function getBlockPosition(): Vector3 { return $this->blockPosition instanceof Block ? ($this->blockPosition->getPosition() ?? Vector3::zero()) : $this->blockPosition; }
    public function getBlock(): Block { return $this->blockPosition instanceof Block ? $this->blockPosition : VanillaBlocks::AIR(); }
    public function getItem(): Item { return clone $this->item; }
    public function getInstaBreak(): bool { return $this->instaBreak; }
    public function setInstaBreak(bool $instaBreak): void { $this->instaBreak = $instaBreak; }
    /** @return Item[] */
    public function getDrops(): array { return $this->drops; }
    /** @param Item[] $drops */
    public function setDrops(array $drops): void { $this->setDropsVariadic(...$drops); }
    public function setDropsVariadic(Item ...$drops): void { $this->drops = $drops; }
    public function getXpDropAmount(): int { return $this->xpDropAmount; }
    public function setXpDropAmount(int $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must be at least zero.');
        }
        $this->xpDropAmount = $amount;
    }
}
