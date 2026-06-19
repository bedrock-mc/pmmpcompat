<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks;

use pocketmine\resourcepacks\json\Manifest;

class ZippedResourcePack implements ResourcePack
{
    private Manifest $manifest;
    private ?string $sha256 = null;

    public function __construct(private string $path)
    {
        if (!is_file($path)) {
            throw new ResourcePackException('File not found');
        }
        if (filesize($path) === 0) {
            throw new ResourcePackException('Empty file, probably corrupted');
        }
        if (!class_exists(\ZipArchive::class)) {
            throw new ResourcePackException('ZipArchive extension is required to read zipped resource packs');
        }
        $archive = new \ZipArchive();
        $openResult = $archive->open($path);
        if ($openResult !== true) {
            throw new ResourcePackException('Encountered ZipArchive error code ' . $openResult . ' while trying to open ' . $path);
        }
        $manifestData = $archive->getFromName('manifest.json');
        if ($manifestData === false) {
            for ($i = 0; $i < $archive->numFiles; $i++) {
                $name = $archive->getNameIndex($i);
                if (is_string($name) && preg_match('#(^|/)manifest\\.json$#', $name) === 1) {
                    $manifestData = $archive->getFromIndex($i);
                    break;
                }
            }
        }
        $archive->close();
        if ($manifestData === false) {
            throw new ResourcePackException('manifest.json not found in the archive');
        }
        $decoded = json_decode($manifestData, true);
        if (!is_array($decoded)) {
            throw new ResourcePackException('Failed to parse manifest.json');
        }
        try {
            $this->manifest = Manifest::fromArray($decoded);
        } catch (\InvalidArgumentException $e) {
            throw new ResourcePackException('Invalid manifest.json contents: ' . $e->getMessage(), 0, $e);
        }
    }

    public function __destruct()
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPackName(): string
    {
        return $this->manifest->header->name;
    }

    public function getPackId(): string
    {
        return $this->manifest->header->uuid;
    }

    public function getPackSize(): int
    {
        return filesize($this->path) ?: 0;
    }

    public function getPackVersion(): string
    {
        return implode('.', $this->manifest->header->version);
    }

    public function getSha256(bool $cached = true): string
    {
        if ($this->sha256 === null || !$cached) {
            $hash = hash_file('sha256', $this->path, true);
            if ($hash === false) {
                throw new ResourcePackException('Failed to hash resource pack');
            }
            $this->sha256 = $hash;
        }
        return $this->sha256;
    }

    public function getPackChunk(int $start, int $length): string
    {
        if ($length < 1) {
            throw new \InvalidArgumentException('Pack length must be positive');
        }
        if ($start < 0 || $start >= $this->getPackSize()) {
            throw new \InvalidArgumentException('Requested a resource pack chunk with invalid start offset');
        }
        $handle = fopen($this->path, 'rb');
        if ($handle === false) {
            throw new ResourcePackException('Failed to open resource pack');
        }
        try {
            fseek($handle, $start);
            $data = fread($handle, $length);
            return $data === false ? '' : $data;
        } finally {
            fclose($handle);
        }
    }
}
