<?php

declare(strict_types=1);

namespace pocketmine\block;

class Cake extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:cake', 'Cake'); }
    public const MAX_BITES = 0;
    public function getBites(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDropsForCompatibleTool(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getResidue(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setBites(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
