<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\compression;

use pocketmine\utils\SingletonTrait;

use const ZLIB_ENCODING_RAW;

class ZlibCompressor implements Compressor
{
    use SingletonTrait;

    public const DEFAULT_LEVEL = 7;
    public const DEFAULT_MAX_DECOMPRESSION_SIZE = 8 * 1024 * 1024;
    public const DEFAULT_THRESHOLD = 256;

    public function __construct(
        private int $level = self::DEFAULT_LEVEL,
        private ?int $minCompressionSize = self::DEFAULT_THRESHOLD,
        private int $maxDecompressionSize = self::DEFAULT_MAX_DECOMPRESSION_SIZE
    ) {}

    public function compress(string $payload): string
    {
        $level = $this->minCompressionSize !== null && strlen($payload) >= $this->minCompressionSize ? $this->level : 0;
        $result = zlib_encode($payload, ZLIB_ENCODING_RAW, $level);
        if ($result === false) {
            throw new \RuntimeException('ZLIB compression failed');
        }
        return $result;
    }

    public function decompress(string $payload): string
    {
        $result = @zlib_decode($payload, $this->maxDecompressionSize);
        if ($result === false) {
            throw new DecompressionException('Failed to decompress data');
        }
        return $result;
    }

    public function getCompressionThreshold(): ?int { return $this->minCompressionSize; }
    public function getNetworkId(): int { return 0; }
}
