<?php

declare(strict_types=1);

namespace pocketmine\event\player;

use pocketmine\block\Block;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class PlayerInteractEvent extends PlayerEvent implements Cancellable
{
    use CancellableTrait;

    public const LEFT_CLICK_BLOCK = 0;
    public const RIGHT_CLICK_BLOCK = 1;
    public const LEFT_CLICK_AIR = 2;
    public const RIGHT_CLICK_AIR = 3;
    public const PHYSICAL = 4;
    private bool $useItem = true;
    private bool $useBlock = true;
    private int $face;

    public function __construct(
        Player $player,
        private Item $item,
        private ?Block $block = null,
        private ?Vector3 $touchVector = null,
        private int $action = self::RIGHT_CLICK_AIR,
        ?int $face = null,
    ) {
        parent::__construct($player);
        $this->face = $face ?? 0;
    }

    public function getItem(): Item { return $this->item; }
    public function getBlock(): ?Block { return $this->block; }
    public function getTouchVector(): Vector3 { return $this->touchVector ?? Vector3::zero(); }
    public function getAction(): int { return $this->action; }
    public function getFace(): int { return $this->face; }
    public function useItem(): bool { return $this->useItem; }
    public function setUseItem(bool $useItem): void { $this->useItem = $useItem; }
    public function useBlock(): bool { return $this->useBlock; }
    public function setUseBlock(bool $useBlock): void { $this->useBlock = $useBlock; }
}
