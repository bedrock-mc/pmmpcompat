<?php

declare(strict_types=1);

namespace pocketmine\block;

class MobHead extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:mobhead', 'MobHead'); }
    public const MAX_ROTATION = 0;
    public const MIN_ROTATION = 0;
    public function describeBlockItemState(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function getFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getMobHeadType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setFacing(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setMobHeadType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setRotation(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
