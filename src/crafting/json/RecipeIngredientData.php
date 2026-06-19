<?php

declare(strict_types=1);

namespace pocketmine\crafting\json;

class RecipeIngredientData
{
    public const WILDCARD_META_VALUE = 32767;

    public string $name = '';
    public int $meta = self::WILDCARD_META_VALUE;
    public string $block_states = '';
    public string $tag = '';
    public string $molang_expression = '';
    public int $molang_version = 0;
    public int $count = 1;
}
