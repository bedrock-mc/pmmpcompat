<?php

declare(strict_types=1);

namespace pocketmine\world\format\io\data;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;

class JavaWorldData extends BaseNbtWorldData
{
    private const TAG_DAY_TIME = 'DayTime';
    private const TAG_DIFFICULTY = 'Difficulty';
    private const TAG_FORMAT_VERSION = 'version';
    private const TAG_GAME_RULES = 'GameRules';
    private const TAG_GAME_TYPE = 'GameType';
    private const TAG_GENERATOR_VERSION = 'generatorVersion';
    private const TAG_HARDCORE = 'hardcore';
    private const TAG_INITIALIZED = 'initialized';
    private const TAG_LAST_PLAYED = 'LastPlayed';
    private const TAG_LIGHTNING_LEVEL = 'thunderLevel';
    private const TAG_LIGHTNING_TIME = 'thunderTime';
    private const TAG_RAIN_LEVEL = 'rainLevel';
    private const TAG_RAIN_TIME = 'rainTime';
    private const TAG_SIZE_ON_DISK = 'SizeOnDisk';

    public static function generate(string $path, string $name, WorldCreationOptions $options, int $version = 19133): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $spawn = $options->getSpawnPosition();
        $data = CompoundTag::create()
            ->setByte(self::TAG_HARDCORE, 0)
            ->setByte(self::TAG_DIFFICULTY, $options->getDifficulty())
            ->setByte(self::TAG_INITIALIZED, 1)
            ->setInt(self::TAG_GAME_TYPE, 0)
            ->setInt(self::TAG_GENERATOR_VERSION, 1)
            ->setInt(self::TAG_FORMAT_VERSION, $version)
            ->setLong(self::TAG_DAY_TIME, 0)
            ->setLong(self::TAG_LAST_PLAYED, (int) floor(microtime(true) * 1000))
            ->setLong(self::TAG_RANDOM_SEED, $options->getSeed())
            ->setLong(self::TAG_SIZE_ON_DISK, 0)
            ->setLong(self::TAG_TIME, 0)
            ->setString(self::TAG_GENERATOR_NAME, self::generatorName($options))
            ->setString(self::TAG_GENERATOR_OPTIONS, $options->getGeneratorOptions())
            ->setString(self::TAG_LEVEL_NAME, $name)
            ->setInt(self::TAG_SPAWN_X, (int) floor($spawn->x))
            ->setInt(self::TAG_SPAWN_Y, (int) floor($spawn->y))
            ->setInt(self::TAG_SPAWN_Z, (int) floor($spawn->z))
            ->setTag(self::TAG_GAME_RULES, CompoundTag::create())
            ->setFloat(self::TAG_LIGHTNING_LEVEL, 0.0)
            ->setInt(self::TAG_LIGHTNING_TIME, 0)
            ->setFloat(self::TAG_RAIN_LEVEL, 0.0)
            ->setInt(self::TAG_RAIN_TIME, 0);

        file_put_contents($path . DIRECTORY_SEPARATOR . 'level.dat', json_encode($data->toCompatibilityData(), JSON_THROW_ON_ERROR));
    }

    protected function load(): CompoundTag
    {
        return $this->loadCompatibilityJson([
            self::TAG_LEVEL_NAME => basename(dirname($this->dataPath)),
            self::TAG_GENERATOR_NAME => 'DEFAULT',
            self::TAG_GENERATOR_OPTIONS => '',
            self::TAG_RANDOM_SEED => 0,
            self::TAG_TIME => 0,
            self::TAG_SPAWN_X => 0,
            self::TAG_SPAWN_Y => 64,
            self::TAG_SPAWN_Z => 0,
            self::TAG_DIFFICULTY => World::DIFFICULTY_NORMAL,
            self::TAG_LIGHTNING_LEVEL => 0.0,
            self::TAG_LIGHTNING_TIME => 0,
            self::TAG_RAIN_LEVEL => 0.0,
            self::TAG_RAIN_TIME => 0,
        ]);
    }

    protected function fix(): void
    {
        if (($fixed = self::hackyFixForGeneratorClasspathInLevelDat($this->getGenerator())) !== null) {
            $this->compoundTag->setString(self::TAG_GENERATOR_NAME, $fixed);
        }
    }

    public function save(): void
    {
        $this->saveCompatibilityJson();
    }

    public function getDifficulty(): int
    {
        return $this->compoundTag->getInt(self::TAG_DIFFICULTY, World::DIFFICULTY_NORMAL);
    }

    public function setDifficulty(int $difficulty): void
    {
        $this->compoundTag->setInt(self::TAG_DIFFICULTY, $difficulty);
    }

    public function getLightningLevel(): float
    {
        return $this->compoundTag->getFloat(self::TAG_LIGHTNING_LEVEL, 0.0);
    }

    public function setLightningLevel(float $level): void
    {
        $this->compoundTag->setFloat(self::TAG_LIGHTNING_LEVEL, $level);
    }

    public function getLightningTime(): int
    {
        return $this->compoundTag->getInt(self::TAG_LIGHTNING_TIME, 0);
    }

    public function setLightningTime(int $ticks): void
    {
        $this->compoundTag->setInt(self::TAG_LIGHTNING_TIME, $ticks);
    }

    public function getRainLevel(): float
    {
        return $this->compoundTag->getFloat(self::TAG_RAIN_LEVEL, 0.0);
    }

    public function setRainLevel(float $level): void
    {
        $this->compoundTag->setFloat(self::TAG_RAIN_LEVEL, $level);
    }

    public function getRainTime(): int
    {
        return $this->compoundTag->getInt(self::TAG_RAIN_TIME, 0);
    }

    public function setRainTime(int $ticks): void
    {
        $this->compoundTag->setInt(self::TAG_RAIN_TIME, $ticks);
    }

    private static function generatorName(WorldCreationOptions $options): string
    {
        try {
            return (new GeneratorManager())->getGeneratorName($options->getGeneratorClass());
        } catch (\InvalidArgumentException) {
            return $options->getGeneratorClass();
        }
    }
}
