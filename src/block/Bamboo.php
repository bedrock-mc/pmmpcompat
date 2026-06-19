<?php

declare(strict_types=1);

namespace pocketmine\block;

class Bamboo extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bamboo', 'Bamboo'); }
    public const LARGE_LEAVES = 0;
    public const NO_LEAVES = 0;
    public const SMALL_LEAVES = 0;
    public function asItem(): \pocketmine\item\Item { return $this->compatMethod(__FUNCTION__, []); }
    public function getLeafSize(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getModelPositionOffset(): ?\pocketmine\math\Vector3 { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isThick(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onRandomTick(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setLeafSize(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setReady(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setThick(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function ticksRandomly(): bool { return $this->compatMethod(__FUNCTION__, []); }
}
