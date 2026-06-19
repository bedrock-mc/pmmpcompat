<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

interface BrewingRecipe
{
    public function getResultFor(Item $input): ?Item;
}
