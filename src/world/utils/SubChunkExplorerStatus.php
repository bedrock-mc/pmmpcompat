<?php

declare(strict_types=1);

namespace pocketmine\world\utils;

/**
 * Status values returned while walking subchunks.
 */
final class SubChunkExplorerStatus
{
    public const INVALID = 0;
    public const OK = 1;
    public const MOVED = 2;

    private function __construct() {}
}
