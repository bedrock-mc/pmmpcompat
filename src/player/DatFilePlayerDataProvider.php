<?php

declare(strict_types=1);

namespace pocketmine\player;

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\utils\Filesystem;

/**
 * Stores local compatibility player data in gzip-compressed NBT-compatible JSON.
 *
 * This preserves the PMMP API contract for plugins running in the bridge. It is
 * intentionally not used as direct Dragonfly player persistence.
 */
final class DatFilePlayerDataProvider implements PlayerDataProvider
{
    public function __construct(private string $path)
    {
        if (!is_dir($this->path) && !mkdir($this->path, 0777, true) && !is_dir($this->path)) {
            throw new \RuntimeException('Failed to create player data directory: ' . $this->path);
        }
    }

    private function getPlayerDataPath(string $username): string
    {
        return rtrim($this->path, '/\\') . DIRECTORY_SEPARATOR . strtolower($username) . '.dat';
    }

    public function hasData(string $name): bool
    {
        return is_file($this->getPlayerDataPath($name));
    }

    public function loadData(string $name): ?CompoundTag
    {
        $path = $this->getPlayerDataPath($name);
        if (!is_file($path)) {
            return null;
        }

        try {
            $contents = Filesystem::fileGetContents($path);
            $decoded = @gzdecode($contents);
            if ($decoded === false) {
                $this->moveCorruptedData($path);
                throw new PlayerDataLoadException('Failed to decompress player data file: ' . $path);
            }
            return (new BigEndianNbtSerializer())->read($decoded)->mustGetCompoundTag();
        } catch (PlayerDataLoadException $e) {
            throw $e;
        } catch (NbtDataException $e) {
            $this->moveCorruptedData($path);
            throw new PlayerDataLoadException('Failed to decode player data file "' . $path . '": ' . $e->getMessage(), 0, $e);
        } catch (\RuntimeException $e) {
            throw new PlayerDataLoadException('Failed to read player data file "' . $path . '": ' . $e->getMessage(), 0, $e);
        }
    }

    public function saveData(string $name, CompoundTag $data): void
    {
        $encoded = gzencode((new BigEndianNbtSerializer())->write(new TreeRoot($data)));
        if ($encoded === false) {
            throw new PlayerDataSaveException('Failed to compress player data');
        }

        try {
            Filesystem::safeFilePutContents($this->getPlayerDataPath($name), $encoded);
        } catch (\RuntimeException $e) {
            throw new PlayerDataSaveException('Failed to write player data file: ' . $e->getMessage(), 0, $e);
        }
    }

    private function moveCorruptedData(string $path): void
    {
        if (is_file($path)) {
            @rename($path, $path . '.bak');
        }
    }
}
