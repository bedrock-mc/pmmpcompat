<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

interface RecipeIngredient extends \Stringable
{
    public function accepts(Item $item): bool;
}
