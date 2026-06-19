<?php

declare(strict_types=1);

namespace pocketmine\block;

class MonsterSpawner extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:monsterspawner', 'MonsterSpawner'); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
}
