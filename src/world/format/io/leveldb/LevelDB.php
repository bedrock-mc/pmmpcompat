<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\leveldb;

use pocketmine\world\format\io\BaseWorldProvider;
use pocketmine\world\format\io\ChunkData;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\format\io\LoadedChunkData;
use pocketmine\world\format\io\WritableWorldProvider;

class LevelDB extends BaseWorldProvider implements WritableWorldProvider
{
    /** @var array<string, string> */
    private array $db = [];
    private string $dbFile;

    public function __construct(string $path = '', mixed $logger = null)
    {
        parent::__construct($path);
        $dbPath = $path . '/db';
        if ($path !== '' && !is_dir($dbPath)) {
            mkdir($dbPath, 0777, true);
        }
        $this->dbFile = $dbPath . '/pmmpcompat-leveldb.json';
        if (is_file($this->dbFile)) {
            $decoded = json_decode((string) file_get_contents($this->dbFile), true);
            if (is_array($decoded)) {
                foreach ($decoded as $entry) {
                    if (is_array($entry) && is_string($entry['key'] ?? null) && is_string($entry['value'] ?? null)) {
                        $key = base64_decode($entry['key'], true);
                        $payload = base64_decode($entry['value'], true);
                        if ($key !== false && $payload !== false) {
                            $this->db[$key] = $payload;
                        }
                    }
                }
            }
        }
    }

    public static function isValid(string $path): bool
    {
        return is_file($path . '/level.dat') && is_dir($path . '/db');
    }

    public static function generate(string $path, string $name, mixed $options = null): void
    {
        if (!is_dir($path . '/db')) {
            mkdir($path . '/db', 0777, true);
        }
        if (!is_file($path . '/level.dat')) {
            file_put_contents($path . '/level.dat', json_encode(['name' => $name, 'format' => 'leveldb'], JSON_PRETTY_PRINT));
        }
    }

    public static function chunkIndex(int $chunkX, int $chunkZ): string
    {
        return pack('V', $chunkX & 0xffffffff) . pack('V', $chunkZ & 0xffffffff);
    }

    public function getWorldMinY(): int
    {
        return -64;
    }

    public function getWorldMaxY(): int
    {
        return 320;
    }

    public function getDatabase(): object
    {
        return new class($this->db) implements \IteratorAggregate {
            public function __construct(private array &$db) {}
            public function get(string $key): string|false { return $this->db[$key] ?? false; }
            public function put(string $key, string $value): void { $this->db[$key] = $value; }
            public function delete(string $key): void { unset($this->db[$key]); }
            public function getIterator(): \Traversable { return new \ArrayIterator($this->db); }
        };
    }

    public function saveChunk(int $chunkX, int $chunkZ, ChunkData $chunkData, int $dirtyFlags): void
    {
        $index = self::chunkIndex($chunkX, $chunkZ);
        $this->db[$index . ChunkDataKey::NEW_VERSION] = chr(ChunkVersion::v1_21_40);
        $this->db[$index . ChunkDataKey::PM_DATA_VERSION] = '1';
        $this->db[$index . ChunkDataKey::LEGACY_TERRAIN] = FastChunkSerializer::serializeTerrain(new LoadedChunkData($chunkData));
        $this->flush();
    }

    public function loadChunk(int $chunkX, int $chunkZ): ?LoadedChunkData
    {
        $index = self::chunkIndex($chunkX, $chunkZ);
        $raw = $this->db[$index . ChunkDataKey::LEGACY_TERRAIN] ?? null;
        if ($raw === null) {
            return null;
        }
        $decoded = FastChunkSerializer::deserializeTerrain($raw);
        return $decoded instanceof LoadedChunkData ? $decoded : null;
    }

    public function getAllChunks(bool $skipCorrupted = false, ?\Logger $logger = null): \Generator
    {
        foreach ($this->db as $key => $_) {
            if (strlen($key) === 9 && ($key[8] === ChunkDataKey::NEW_VERSION || $key[8] === ChunkDataKey::OLD_VERSION)) {
                $chunkX = self::readSignedLInt(substr($key, 0, 4));
                $chunkZ = self::readSignedLInt(substr($key, 4, 4));
                $chunk = $this->loadChunk($chunkX, $chunkZ);
                if ($chunk !== null) {
                    yield [$chunkX, $chunkZ] => $chunk;
                }
            }
        }
    }

    public function calculateChunkCount(): int
    {
        $seen = [];
        foreach ($this->db as $key => $_) {
            if (strlen($key) === 9 && ($key[8] === ChunkDataKey::NEW_VERSION || $key[8] === ChunkDataKey::OLD_VERSION)) {
                $seen[substr($key, 0, 8)] = true;
            }
        }
        return count($seen);
    }

    public function close(): void
    {
        $this->flush();
    }

    public function doGarbageCollection(): void
    {
    }

    private function flush(): void
    {
        if ($this->dbFile === '/db/pmmpcompat-leveldb.json') {
            return;
        }
        $encoded = [];
        foreach ($this->db as $key => $value) {
            $encoded[] = ['key' => base64_encode($key), 'value' => base64_encode($value)];
        }
        file_put_contents($this->dbFile, json_encode($encoded, JSON_PRETTY_PRINT));
    }

    private static function readSignedLInt(string $bytes): int
    {
        $value = unpack('V', $bytes)[1];
        return $value >= 0x80000000 ? $value - 0x100000000 : $value;
    }
}
