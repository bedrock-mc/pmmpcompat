<?php

declare(strict_types=1);

namespace pocketmine\block;

class RedMushroomBlock extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:redmushroomblock', 'RedMushroomBlock'); }
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getMushroomBlockType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getPickedItem(bool $addUserData = false): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, [$addUserData]); }
    public function getSilkTouchDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function isAffectedBySilkTouch(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function setMushroomBlockType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
