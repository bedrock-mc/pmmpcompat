<?php

declare(strict_types=1);

namespace pocketmine\crafting;

use pocketmine\item\Item;

class CraftingManagerFromDataHelper
{
    public static function deserializeItemStack(mixed $data): ?Item
    {
        if ($data instanceof Item) {
            return clone $data;
        }
        if (is_array($data)) {
            $id = (string) ($data['name'] ?? $data['id'] ?? $data['type_id'] ?? 'minecraft:air');
            $count = (int) ($data['count'] ?? 1);
            return new Item($id, $id, $count);
        }
        if (is_string($data) && $data !== '') {
            return new Item($data, $data);
        }
        return null;
    }

    /** @return object[] */
    public static function loadJsonArrayOfObjectsFile(string $file, string $class): array
    {
        if (!is_file($file)) {
            return [];
        }
        $decoded = json_decode((string) file_get_contents($file), true);
        if (!is_array($decoded)) {
            return [];
        }
        $objects = [];
        foreach ($decoded as $row) {
            if (is_array($row)) {
                $objects[] = new $class(...$row);
            }
        }
        return $objects;
    }

    public static function make(): CraftingManager
    {
        return new CraftingManager();
    }
}
