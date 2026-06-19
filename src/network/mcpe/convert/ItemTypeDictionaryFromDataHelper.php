<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

final class ItemTypeDictionaryFromDataHelper
{
    public static function loadFromString(string $payload): array
    {
        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return ['minecraft:air' => 0, 'minecraft:stone' => 1, 'minecraft:dirt' => 2];
        }
        $result = [];
        foreach ($decoded as $key => $value) {
            if (is_string($key)) {
                $result[$key] = (int) $value;
            } elseif (is_array($value) && isset($value['name'], $value['id'])) {
                $result[(string) $value['name']] = (int) $value['id'];
            }
        }
        return $result;
    }
}
