<?php

declare(strict_types=1);

namespace pocketmine\item;

class GoldenAppleEnchanted extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:goldenappleenchanted', 'GoldenAppleEnchanted'); }
    public function getAdditionalEffects(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
