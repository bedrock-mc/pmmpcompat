<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\block\convert\property;

interface StateMap
{
    public function getRawToValueMap(): array;
    public function printableValue(mixed $value): string;
    public function rawToValue(int|string $raw): mixed;
    public function valueToRaw(mixed $value): int|string;
}
