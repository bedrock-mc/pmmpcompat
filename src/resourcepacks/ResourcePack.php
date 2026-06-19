<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks;

interface ResourcePack
{
    public function getPackName(): string;
    public function getPackId(): string;
    public function getPackSize(): int;
    public function getPackVersion(): string;
    public function getSha256(): string;
    public function getPackChunk(int $start, int $length): string;
}
