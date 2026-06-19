<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\region;

class RegionLoader
{
    public const COMPRESSION_GZIP = 1;
    public const COMPRESSION_ZLIB = 2;
    public const FIRST_SECTOR = 2;

    /** @var array<string, string> */
    private array $chunks = [];
    public int $lastUsed;

    private function __construct(private string $filePath)
    {
        $this->lastUsed = time();
    }

    public static function loadExisting(string $filePath): self
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException("File $filePath does not exist");
        }
        $loader = new self($filePath);
        $raw = file_get_contents($filePath);
        if ($raw === false || $raw === '') {
            return $loader;
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || ($decoded['format'] ?? null) !== 'pmmpcompat-region-v1' || !is_array($decoded['chunks'] ?? null)) {
            throw new CorruptedRegionException('Unsupported pmmpcompat region payload');
        }
        foreach ($decoded['chunks'] as $key => $payload) {
            if (is_string($key) && is_string($payload)) {
                $chunk = base64_decode($payload, true);
                if ($chunk === false) {
                    throw new CorruptedRegionException('Invalid base64 chunk payload');
                }
                $loader->chunks[$key] = $chunk;
            }
        }
        return $loader;
    }

    public static function createNew(string $filePath): self
    {
        if (is_file($filePath)) {
            throw new \RuntimeException("Region file $filePath already exists");
        }
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $loader = new self($filePath);
        $loader->flush();
        return $loader;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function chunkExists(int $x, int $z): bool
    {
        $this->validateChunkCoordinates($x, $z);
        return isset($this->chunks[$x . ':' . $z]);
    }

    public function readChunk(int $x, int $z): ?string
    {
        $this->lastUsed = time();
        $this->validateChunkCoordinates($x, $z);
        return $this->chunks[$x . ':' . $z] ?? null;
    }

    public function writeChunk(int $x, int $z, string $chunkData): void
    {
        $this->lastUsed = time();
        $this->validateChunkCoordinates($x, $z);
        $this->chunks[$x . ':' . $z] = $chunkData;
        $this->flush();
    }

    public function removeChunk(int $x, int $z): void
    {
        $this->lastUsed = time();
        $this->validateChunkCoordinates($x, $z);
        unset($this->chunks[$x . ':' . $z]);
        $this->flush();
    }

    public function calculateChunkCount(): int
    {
        return count($this->chunks);
    }

    public function close(): void
    {
        if ($this->filePath !== '') {
            $this->flush();
        }
    }

    public function generateSectorMap(): RegionGarbageMap
    {
        return new RegionGarbageMap([]);
    }

    public function getProportionUnusedSpace(): float
    {
        return 0.0;
    }

    private function flush(): void
    {
        $payload = ['format' => 'pmmpcompat-region-v1', 'chunks' => []];
        foreach ($this->chunks as $key => $chunk) {
            $payload['chunks'][$key] = base64_encode($chunk);
        }
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        if ($json === false) {
            throw new RegionException('Failed to encode region payload');
        }
        file_put_contents($this->filePath, $json);
    }

    private function validateChunkCoordinates(int $x, int $z): void
    {
        if ($x < 0 || $x > 31 || $z < 0 || $z > 31) {
            throw new \InvalidArgumentException('Region-local chunk coordinates must be between 0 and 31');
        }
    }
}
