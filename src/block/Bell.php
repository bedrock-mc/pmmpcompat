<?php

declare(strict_types=1);

namespace pocketmine\block;

class Bell extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:bell', 'Bell'); }
    public function getAttachmentType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForIncompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onProjectileHit(mixed ...$args): void { $this->compatMethod(__FUNCTION__, $args); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function ring(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setAttachmentType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
