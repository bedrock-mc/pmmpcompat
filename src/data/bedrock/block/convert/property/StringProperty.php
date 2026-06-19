<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

interface StringProperty extends Property
{
    public function deserializePlain(object $block, string $raw): void;
    /** @return string[] */
    public function getPossibleValues(): array;
    public function serializePlain(object $block): string;
}
