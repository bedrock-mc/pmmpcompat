<?php

declare(strict_types=1);

namespace pocketmine\block;

class BigDripleafHead extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bigdripleafhead', 'BigDripleafHead'); }
    public function getLeafState(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasEntityCollision(): bool { return $this->compatMethod(__FUNCTION__, []); }
    public function onEntityInside(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function setLeafState(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
