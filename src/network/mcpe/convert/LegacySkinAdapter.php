<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

final class LegacySkinAdapter implements SkinAdapter
{
    public function fromSkinData(mixed $data): mixed
    {
        if (is_array($data)) {
            return (object) $data;
        }
        return $data;
    }

    public function toSkinData(mixed $skin): mixed
    {
        if (is_object($skin)) {
            return get_object_vars($skin);
        }
        return $skin;
    }
}
