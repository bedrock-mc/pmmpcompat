<?php

declare(strict_types=1);

namespace pocketmine\block;

class ChemistryTable extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:chemistrytable', 'ChemistryTable'); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
}
