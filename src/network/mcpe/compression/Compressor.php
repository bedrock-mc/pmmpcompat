<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\compression;

interface Compressor
{
    public function compress(string $payload): string;
    public function decompress(string $payload): string;
    public function getCompressionThreshold(): ?int;
    public function getNetworkId(): int;
}
