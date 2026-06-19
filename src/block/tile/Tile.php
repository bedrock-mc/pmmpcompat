<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

use pocketmine\inventory\Inventory;

class Tile extends \pocketmine\block\Block
{
    public const TAG_ID = 0;
    public const TAG_X = 0;
    public const TAG_Y = 0;
    public const TAG_Z = 0;

    /** @var array<string, mixed> */
    private array $tileState = [];
    private Inventory $inventory;
    private bool $closed = false;
    private bool $dirty = false;

    public function __construct(mixed ...$args)
    {
        $short = substr(static::class, strrpos(static::class, '\\') + 1);
        parent::__construct('minecraft:' . strtolower($short), $short);
        $this->inventory = new Inventory($this->defaultInventorySize($short));
        $this->tileState['name'] = $short;
    }

    public function __destruct() {}
    public function close(mixed ...$args): mixed { $this->closed = true; return null; }
    public function copyDataFromItem(mixed ...$args): mixed { return $this; }
    public function canOpenWith(mixed ...$args): mixed { return true; }
    public function getBlock(mixed ...$args): mixed { return $this; }
    public function getCleanedNBT(mixed ...$args): mixed { return $this->tileState; }
    public function getDefaultName(mixed ...$args): mixed { return $this->tileState['defaultName'] ?? $this->getName(); }
    public function getInventory(mixed ...$args): mixed { return $this->inventory; }
    public function getName(): string { return (string) ($this->tileState['name'] ?? parent::getName()); }
    public function getPosition(): ?\pocketmine\math\Vector3 { return parent::getPosition(); }
    public function getRealInventory(mixed ...$args): mixed { return $this->inventory; }
    public function hasName(mixed ...$args): mixed { return isset($this->tileState['name']) && $this->tileState['name'] !== ''; }
    public function isClosed(mixed ...$args): mixed { return $this->closed; }
    public function onBlockDestroyed(mixed ...$args): mixed { $this->close(); return null; }
    public function readSaveData(mixed ...$args): mixed { return null; }
    public function saveNBT(mixed ...$args): mixed { return $this->tileState; }
    public function setName(mixed ...$args): mixed { return $this->setTileState('name', (string) ($args[0] ?? '')); }

    /** @param list<mixed> $args */
    protected function compatTileMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'close' => $this->markClosed(),
            'copyDataFromItem' => $this,
            'getBlock' => $this,
            'getPosition' => parent::getPosition(),
            'getCleanedNBT',
            'saveNBT',
            'getSerializedSpawnCompound',
            'getSpawnCompound',
            'getRenderUpdateBugWorkaroundStateProperties' => $this->tileState,
            'readSaveData',
            'clearSpawnCompoundCache',
            'addAdditionalSpawnData',
            'onBlockDestroyed' => null,
            'isClosed' => $this->closed,
            'getDefaultName' => $this->tileState['defaultName'] ?? $this->getName(),
            'getName' => $this->tileState['name'] ?? $this->getDefaultName(),
            'hasName' => isset($this->tileState['name']) && $this->tileState['name'] !== '',
            'setName' => $this->setTileState('name', (string) ($args[0] ?? '')),
            'getInventory',
            'getRealInventory' => $this->inventory,
            'canOpenWith' => true,
            'isDirty' => $this->dirty,
            'setDirty' => $this->setDirtyState((bool) ($args[0] ?? true)),
            'isPaired' => isset($this->tileState['pair']),
            'getPair' => $this->tileState['pair'] ?? null,
            'pairWith' => $this->setTileState('pair', $args[0] ?? null),
            'unpair' => $this->clearTileState('pair'),
            'getViewerCount',
            'getTicks',
            'getSignalStrength',
            'getRotation',
            'getItemRotation',
            'getViewedPage',
            'getLastInteractedSlot',
            'getPitch' => (int) ($this->tileState[$this->propertyKey($method)] ?? 0),
            'getItemDropChance' => (float) ($this->tileState['itemDropChance'] ?? 1.0),
            'isRinging',
            'isWaxed',
            'hasItem' => (bool) ($this->tileState[$this->propertyKey($method)] ?? false),
            default => $this->tileFallback($method, $args),
        };
    }

    /** @param list<mixed> $args */
    public static function compatTileStaticMethod(string $method, array $args): mixed
    {
        return match ($method) {
            'isRegistered' => true,
            'register' => null,
            'getSaveId' => is_object($args[0] ?? null) ? get_class($args[0]) : (string) ($args[0] ?? ''),
            'createFromData' => new Tile(),
            default => null,
        };
    }

    private function tileFallback(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'set')) {
            return $this->setTileState(lcfirst(substr($method, 3)), $args[0] ?? null);
        }
        if (str_starts_with($method, 'get')) {
            return $this->tileState[$this->propertyKey($method)] ?? $this->defaultTileValue($method);
        }
        if (str_starts_with($method, 'has') || str_starts_with($method, 'is')) {
            return (bool) ($this->tileState[$this->propertyKey($method)] ?? false);
        }
        return null;
    }

    private function propertyKey(string $method): string
    {
        return lcfirst(preg_replace('/^(get|set|has|is)/', '', $method) ?? $method);
    }

    private function setTileState(string $key, mixed $value): self
    {
        $this->tileState[$key] = $value;
        $this->dirty = true;
        return $this;
    }

    private function clearTileState(string $key): self
    {
        unset($this->tileState[$key]);
        $this->dirty = true;
        return $this;
    }

    private function setDirtyState(bool $dirty): self
    {
        $this->dirty = $dirty;
        return $this;
    }

    private function markClosed(): null
    {
        $this->closed = true;
        return null;
    }

    private function defaultTileValue(string $method): mixed
    {
        return match ($method) {
            'getColor',
            'getBaseColor',
            'getCustomWaterColor' => new \pocketmine\color\Color(255, 255, 255),
            'getPatterns',
            'getCookingTimes' => [],
            'getText',
            'getBackText' => class_exists(\pocketmine\block\utils\SignText::class) ? new \pocketmine\block\utils\SignText(['']) : '',
            'getItem',
            'getBook',
            'getRecord',
            'getPotionItem',
            'getPlant' => new \pocketmine\item\Item('minecraft:air', 'Air', 0),
            default => null,
        };
    }

    private function defaultInventorySize(string $short): int
    {
        return match ($short) {
            'Furnace', 'BlastFurnace', 'Smoker' => 3,
            'BrewingStand' => 5,
            'Campfire' => 4,
            'Hopper' => 5,
            'ChiseledBookshelf' => 6,
            default => 27,
        };
    }
}
