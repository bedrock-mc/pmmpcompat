<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\serializer;

use pocketmine\network\mcpe\convert\BlockTranslator;

final class ChunkSerializer
{
    private function __construct() {}

    /** @return array{int, int} */
    public static function getDimensionChunkBounds(int $dimensionId): array
    {
        return match ($dimensionId) {
            0 => [-4, 19],
            1 => [0, 7],
            2 => [0, 15],
            default => [0, 15],
        };
    }

    public static function getSubChunkCount(mixed $chunk, int $dimensionId = 0): int
    {
        if (is_object($chunk) && method_exists($chunk, 'getSubChunks')) {
            return count(array_filter($chunk->getSubChunks(), static fn($subChunk): bool => !(is_object($subChunk) && method_exists($subChunk, 'isEmptyFast') && $subChunk->isEmptyFast())));
        }
        return 0;
    }

    public static function serializeFullChunk(mixed $chunk, int $dimensionId = 0, ?BlockTranslator $blockTranslator = null, ?string $tiles = null): string
    {
        return json_encode(['dimension' => $dimensionId, 'subChunks' => self::getSubChunkCount($chunk, $dimensionId), 'tiles' => $tiles ?? self::serializeTiles($chunk)], JSON_THROW_ON_ERROR);
    }

    public static function serializeSubChunk(mixed $subChunk, ?BlockTranslator $blockTranslator = null, mixed $stream = null, bool $persistentBlockStates = false): string
    {
        return json_encode(['empty' => is_object($subChunk) && method_exists($subChunk, 'isEmptyFast') ? $subChunk->isEmptyFast() : false], JSON_THROW_ON_ERROR);
    }

    public static function serializeTiles(mixed $chunk): string
    {
        if (is_object($chunk) && method_exists($chunk, 'getTiles')) {
            return json_encode($chunk->getTiles(), JSON_THROW_ON_ERROR | JSON_PARTIAL_OUTPUT_ON_ERROR);
        }
        return '[]';
    }
}
