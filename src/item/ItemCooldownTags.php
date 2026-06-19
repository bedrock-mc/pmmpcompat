<?php

declare(strict_types=1);

namespace pocketmine\item;

class ItemCooldownTags extends \pocketmine\item\Item
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:itemcooldowntags', 'ItemCooldownTags'); }
    public const CHORUS_FRUIT = 0;
    public const ENDER_PEARL = 0;
    public const GOAT_HORN = 0;
    public const SHIELD = 0;
}
