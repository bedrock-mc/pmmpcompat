<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\leveldb;

class ChunkDataKey
{
    public const HEIGHTMAP_AND_3D_BIOMES = "\x2b";
    public const NEW_VERSION = "\x2c";
    public const HEIGHTMAP_AND_2D_BIOMES = "\x2d";
    public const HEIGHTMAP_AND_2D_BIOME_COLORS = "\x2e";
    public const SUBCHUNK = "\x2f";
    public const LEGACY_TERRAIN = "\x30";
    public const BLOCK_ENTITIES = "\x31";
    public const ENTITIES = "\x32";
    public const PENDING_SCHEDULED_TICKS = "\x33";
    public const LEGACY_BLOCK_EXTRA_DATA = "\x34";
    public const BIOME_STATES = "\x35";
    public const FINALIZATION = "\x36";
    public const CONVERTER_TAG = "\x37";
    public const BORDER_BLOCKS = "\x38";
    public const HARDCODED_SPAWNERS = "\x39";
    public const PENDING_RANDOM_TICKS = "\x3a";
    public const XXHASH_CHECKSUMS = "\x3b";
    public const GENERATION_SEED = "\x3c";
    public const GENERATED_BEFORE_CNC_BLENDING = "\x3d";
    public const OLD_VERSION = "\x76";
    public const PM_DATA_VERSION = 'PMMPDataVersion';
}
