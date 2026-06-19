<?php

declare(strict_types=1);

namespace pocketmine\entity;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\world\World;
use function count;

class EntityFactory
{
    public const TAG_IDENTIFIER = 'identifier';
    public const TAG_LEGACY_ID = 'id';

    private static ?self $instance = null;

    /** @var array<int|string, \Closure> */
    private array $creationFuncs = [];
    /** @var array<class-string<Entity>, string> */
    private array $saveNames = [];

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function __construct()
    {
        $this->register(Squid::class, fn(?World $world, CompoundTag $nbt): Squid => new Squid(EntityDataHelper::parseLocation($nbt, $world), $nbt), ['Squid', 'minecraft:squid']);
        $this->register(Villager::class, fn(?World $world, CompoundTag $nbt): Villager => new Villager(EntityDataHelper::parseLocation($nbt, $world), $nbt), ['Villager', 'minecraft:villager']);
        $this->register(Zombie::class, fn(?World $world, CompoundTag $nbt): Zombie => new Zombie(EntityDataHelper::parseLocation($nbt, $world), $nbt), ['Zombie', 'minecraft:zombie']);
        $this->register(Human::class, fn(?World $world, CompoundTag $nbt): Human => new Human(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt), ['Human']);
    }

    public function createFromData(World $world, CompoundTag $nbt): ?Entity
    {
        $identifier = $this->readSaveId($nbt);
        if ($identifier === null || !isset($this->creationFuncs[$identifier])) {
            return null;
        }

        return ($this->creationFuncs[$identifier])($world, $nbt);
    }

    public function getSaveId(string $className): ?string
    {
        return $this->saveNames[$className] ?? null;
    }

    public function injectSaveId(string $className, CompoundTag $nbt): void
    {
        $saveId = $this->getSaveId($className);
        if ($saveId === null) {
            if (is_a($className, NeverSavedWithChunkEntity::class, true)) {
                return;
            }
            throw new \InvalidArgumentException($className . ' is not registered with EntityFactory');
        }

        $nbt->setString(self::TAG_IDENTIFIER, $saveId);
    }

    public function isRegistered(string $className): bool
    {
        return isset($this->saveNames[$className]);
    }

    /**
     * @param class-string<Entity> $className
     * @param list<string> $saveNames
     */
    public function register(string $className, \Closure $creationFunc, array $saveNames): void
    {
        if (count($saveNames) === 0) {
            throw new \InvalidArgumentException('At least one save name must be provided');
        }
        if (!is_a($className, Entity::class, true)) {
            throw new \InvalidArgumentException($className . ' must extend ' . Entity::class);
        }

        foreach ($saveNames as $name) {
            $this->creationFuncs[$name] = $creationFunc;
        }
        $this->saveNames[$className] = $saveNames[0];
    }

    private function readSaveId(CompoundTag $nbt): int|string|null
    {
        $identifierTag = $nbt->getTag(self::TAG_IDENTIFIER);
        if ($identifierTag instanceof StringTag) {
            return $identifierTag->getValue();
        }
        if ($nbt->getTag(self::TAG_LEGACY_ID) !== null) {
            return $nbt->getInt(self::TAG_LEGACY_ID);
        }
        return null;
    }
}
