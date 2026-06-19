<?php

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\lang\Translatable;

class CreativeGroup
{
    public function __construct(private Translatable|string $name, private Item $icon)
    {
        $text = $name instanceof Translatable ? $name->getText() : $name;
        if ($text === '') {
            throw new \InvalidArgumentException('Creative group name cannot be empty');
        }
    }

    public function getIcon(): Item { return clone $this->icon; }
    public function getName(): Translatable|string { return $this->name; }
}
