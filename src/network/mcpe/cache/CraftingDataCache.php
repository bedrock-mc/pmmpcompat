<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\cache;

use pocketmine\utils\SingletonTrait;

class CraftingDataCache
{
    use SingletonTrait;

    public const RECIPE_ID_OFFSET = 1;

    /** @var array<int, object> */
    private array $caches = [];

    public function getCache(mixed $manager): object
    {
        $id = is_object($manager) ? spl_object_id($manager) : crc32(serialize($manager));
        return $this->caches[$id] ??= (object) [
            'manager' => $manager,
            'recipeIdOffset' => self::RECIPE_ID_OFFSET,
            'recipes' => is_object($manager) && method_exists($manager, 'getCraftingRecipeIndex') ? $manager->getCraftingRecipeIndex() : [],
        ];
    }
}
