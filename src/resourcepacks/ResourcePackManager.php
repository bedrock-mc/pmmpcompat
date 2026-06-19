<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks;

class ResourcePackManager
{
    private bool $serverForceResources = false;
    /** @var list<ResourcePack> */
    private array $resourcePacks = [];
    /** @var array<string, ResourcePack> */
    private array $uuidList = [];
    /** @var array<string, string> */
    private array $encryptionKeys = [];

    public function __construct(private string $path, mixed $logger = null)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $configPath = $path . '/resource_packs.yml';
        if (!is_file($configPath)) {
            file_put_contents($configPath, "force_resources: false\nresource_stack: []\n");
        }
        $this->loadConfig($configPath);
    }

    public function getPath(): string
    {
        return rtrim($this->path, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function resourcePacksRequired(): bool
    {
        return $this->serverForceResources;
    }

    public function setResourcePacksRequired(bool $value): void
    {
        $this->serverForceResources = $value;
    }

    public function getResourceStack(): array
    {
        return $this->resourcePacks;
    }

    public function setResourceStack(array $resourceStack): void
    {
        $uuidList = [];
        $packs = [];
        foreach ($resourceStack as $pack) {
            if (!$pack instanceof ResourcePack) {
                throw new \InvalidArgumentException('Resource stack must contain ResourcePack instances');
            }
            $uuid = strtolower($pack->getPackId());
            if (isset($uuidList[$uuid])) {
                throw new \InvalidArgumentException('Cannot load two resource packs with the same UUID (' . $uuid . ')');
            }
            $uuidList[$uuid] = $pack;
            $packs[] = $pack;
        }
        $this->uuidList = $uuidList;
        $this->resourcePacks = $packs;
    }

    public function getPackById(string $id): ?ResourcePack
    {
        return $this->uuidList[strtolower($id)] ?? null;
    }

    public function getPackIdList(): array
    {
        return array_keys($this->uuidList);
    }

    public function getPackEncryptionKey(string $id): ?string
    {
        return $this->encryptionKeys[strtolower($id)] ?? null;
    }

    public function setPackEncryptionKey(string $id, ?string $key): void
    {
        $id = strtolower($id);
        if ($key === null) {
            unset($this->encryptionKeys[$id]);
            return;
        }
        if (!isset($this->uuidList[$id])) {
            throw new \InvalidArgumentException('Unknown pack ID ' . $id);
        }
        if (strlen($key) !== 32) {
            throw new \InvalidArgumentException('Encryption key must be exactly 32 bytes long');
        }
        $this->encryptionKeys[$id] = $key;
    }

    private function loadConfig(string $configPath): void
    {
        $raw = file($configPath, FILE_IGNORE_NEW_LINES) ?: [];
        $stack = [];
        foreach ($raw as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }
            if (preg_match('/^force_resources:\\s*(true|false|1|0)\\s*$/i', $trimmed, $matches) === 1) {
                $this->serverForceResources = in_array(strtolower($matches[1]), ['true', '1'], true);
                continue;
            }
            if (preg_match('/^-\\s*(.+)$/', $trimmed, $matches) === 1) {
                $stack[] = trim($matches[1], " \t\"'");
            }
        }
        foreach ($stack as $packName) {
            $pack = $this->loadPackFromPath($this->path . '/' . $packName);
            $this->setResourceStack([...$this->resourcePacks, $pack]);
            $keyPath = $this->path . '/' . $packName . '.key';
            if (is_file($keyPath)) {
                $key = rtrim((string) file_get_contents($keyPath), "\r\n");
                $this->setPackEncryptionKey($pack->getPackId(), $key);
            }
        }
    }

    private function loadPackFromPath(string $packPath): ResourcePack
    {
        if (!is_file($packPath)) {
            throw new ResourcePackException('File or directory not found');
        }
        $extension = strtolower(pathinfo($packPath, PATHINFO_EXTENSION));
        return match ($extension) {
            'zip', 'mcpack' => new ZippedResourcePack($packPath),
            default => throw new ResourcePackException('Format not recognized'),
        };
    }
}
