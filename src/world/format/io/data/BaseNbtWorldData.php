<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\data;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\world\format\io\exception\CorruptedWorldException;
use pocketmine\world\format\io\exception\UnsupportedWorldFormatException;
use pocketmine\world\format\io\WorldData;

abstract class BaseNbtWorldData implements WorldData
{
    protected const TAG_LEVEL_NAME = 'LevelName';
    protected const TAG_GENERATOR_NAME = 'generatorName';
    protected const TAG_GENERATOR_OPTIONS = 'generatorOptions';
    protected const TAG_RANDOM_SEED = 'RandomSeed';
    protected const TAG_TIME = 'Time';
    protected const TAG_SPAWN_X = 'SpawnX';
    protected const TAG_SPAWN_Y = 'SpawnY';
    protected const TAG_SPAWN_Z = 'SpawnZ';

    protected CompoundTag $compoundTag;

    /**
     * @throws CorruptedWorldException
     * @throws UnsupportedWorldFormatException
     */
    public function __construct(protected string $dataPath)
    {
        if (!file_exists($this->dataPath)) {
            throw new CorruptedWorldException("World data not found at $dataPath");
        }

        try {
            $this->compoundTag = $this->load();
        } catch (CorruptedWorldException $e) {
            throw new CorruptedWorldException('Corrupted world data: ' . $e->getMessage(), 0, $e);
        }
        $this->fix();
    }

    abstract protected function load(): CompoundTag;

    abstract protected function fix(): void;

    protected static function hackyFixForGeneratorClasspathInLevelDat(string $className): ?string
    {
        return match ($className) {
            'pocketmine\level\generator\normal\Normal' => 'normal',
            'pocketmine\level\generator\Flat' => 'flat',
            default => null,
        };
    }

    public function getCompoundTag(): CompoundTag
    {
        return $this->compoundTag;
    }

    public function getName(): string
    {
        return $this->compoundTag->getString(self::TAG_LEVEL_NAME);
    }

    public function setName(string $value): void
    {
        $this->compoundTag->setString(self::TAG_LEVEL_NAME, $value);
    }

    public function getGenerator(): string
    {
        return $this->compoundTag->getString(self::TAG_GENERATOR_NAME, 'DEFAULT');
    }

    public function getGeneratorOptions(): string
    {
        return $this->compoundTag->getString(self::TAG_GENERATOR_OPTIONS, '');
    }

    public function getSeed(): int
    {
        return $this->compoundTag->getLong(self::TAG_RANDOM_SEED);
    }

    public function getTime(): int
    {
        $timeTag = $this->compoundTag->getTag(self::TAG_TIME);
        return $timeTag instanceof IntTag ? $timeTag->getValue() : $this->compoundTag->getLong(self::TAG_TIME, 0);
    }

    public function setTime(int $value): void
    {
        $this->compoundTag->setLong(self::TAG_TIME, $value);
    }

    public function getSpawn(): Vector3
    {
        return new Vector3(
            $this->compoundTag->getInt(self::TAG_SPAWN_X),
            $this->compoundTag->getInt(self::TAG_SPAWN_Y),
            $this->compoundTag->getInt(self::TAG_SPAWN_Z),
        );
    }

    public function setSpawn(Vector3 $pos): void
    {
        $this->compoundTag->setInt(self::TAG_SPAWN_X, (int) floor($pos->x));
        $this->compoundTag->setInt(self::TAG_SPAWN_Y, (int) floor($pos->y));
        $this->compoundTag->setInt(self::TAG_SPAWN_Z, (int) floor($pos->z));
    }

    protected function loadCompatibilityJson(array $defaults): CompoundTag
    {
        $raw = file_get_contents($this->dataPath);
        if ($raw === false || $raw === '') {
            return $this->tagFromDefaults($defaults);
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['type'], $decoded['value'])) {
            $tag = CompoundTag::fromCompatibilityData($decoded);
            if ($tag instanceof CompoundTag) {
                return $tag;
            }
        }
        if (is_array($decoded)) {
            return $this->tagFromDefaults($decoded + $defaults);
        }
        return $this->tagFromDefaults($defaults);
    }

    /**
     * @param array<string, mixed> $values
     */
    protected function tagFromDefaults(array $values): CompoundTag
    {
        $tag = CompoundTag::create();
        foreach ($values as $name => $value) {
            if (is_int($value)) {
                $tag->setLong($name, $value);
            } elseif (is_float($value)) {
                $tag->setFloat($name, $value);
            } elseif (is_bool($value)) {
                $tag->setByte($name, $value ? 1 : 0);
            } else {
                $tag->setString($name, (string) $value);
            }
        }
        return $tag;
    }

    protected function saveCompatibilityJson(): void
    {
        file_put_contents($this->dataPath, json_encode($this->compoundTag->toCompatibilityData(), JSON_THROW_ON_ERROR));
    }
}
