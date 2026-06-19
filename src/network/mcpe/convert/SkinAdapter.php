<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

interface SkinAdapter
{
    public function fromSkinData(mixed $data): mixed;
    public function toSkinData(mixed $skin): mixed;
}
