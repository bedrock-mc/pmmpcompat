<?php

declare(strict_types=1);

namespace pocketmine\block;

class TorchflowerCrop extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:torchflowercrop', 'TorchflowerCrop'); }
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function describeBlockOnlyState(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
