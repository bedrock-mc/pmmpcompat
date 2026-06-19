<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use pocketmine\nbt\tag\CompoundTag;

class BigEndianNbtSerializer
{
    public function read(string $buffer, int &$offset = 0, int $maxDepth = 0): TreeRoot
    {
        $json = substr($buffer, $offset);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new NbtDataException('Invalid compatibility NBT payload');
        }
        $offset = strlen($buffer);
        return new TreeRoot(CompoundTag::fromCompatibilityData($decoded));
    }

    public function write(TreeRoot $root): string
    {
        $encoded = json_encode($root->getTag()->toCompatibilityData(), JSON_THROW_ON_ERROR);
        return $encoded;
    }
}
