<?php

declare(strict_types=1);

namespace pocketmine\nbt;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;

class TreeRoot
{
    public function __construct(private Tag $tag)
    {
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function mustGetCompoundTag(): CompoundTag
    {
        if (!$this->tag instanceof CompoundTag) {
            throw new NbtDataException('Root tag is not a compound tag');
        }
        return $this->tag;
    }
}
