<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

class PaintingMotive
{
    private static bool $initialized = false;

    /** @var array<string, PaintingMotive> */
    private static array $motives = [];

    public function __construct(
        private int $width,
        private int $height,
        private string $name
    ) {}

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;
        foreach ([
            new self(1, 1, 'Alban'),
            new self(1, 1, 'Aztec'),
            new self(1, 1, 'Aztec2'),
            new self(1, 1, 'Bomb'),
            new self(1, 1, 'Kebab'),
            new self(1, 1, 'meditative'),
            new self(1, 1, 'Plant'),
            new self(1, 1, 'Wasteland'),
            new self(1, 2, 'Graham'),
            new self(1, 2, 'prairie_ride'),
            new self(1, 2, 'Wanderer'),
            new self(2, 1, 'Courbet'),
            new self(2, 1, 'Creebet'),
            new self(2, 1, 'Pool'),
            new self(2, 1, 'Sea'),
            new self(2, 1, 'Sunset'),
            new self(2, 2, 'Bust'),
            new self(2, 2, 'baroque'),
            new self(2, 2, 'Earth'),
            new self(2, 2, 'Fire'),
            new self(2, 2, 'humble'),
            new self(2, 2, 'Match'),
            new self(2, 2, 'SkullAndRoses'),
            new self(2, 2, 'Stage'),
            new self(2, 2, 'Void'),
            new self(2, 2, 'Water'),
            new self(2, 2, 'Wind'),
            new self(2, 2, 'Wither'),
            new self(3, 3, 'bouquet'),
            new self(3, 3, 'cavebird'),
            new self(3, 3, 'cotan'),
            new self(3, 3, 'endboss'),
            new self(3, 3, 'fern'),
            new self(3, 3, 'owlemons'),
            new self(3, 3, 'sunflowers'),
            new self(3, 3, 'tides'),
            new self(3, 4, 'backyard'),
            new self(3, 4, 'pond'),
            new self(4, 2, 'changing'),
            new self(4, 2, 'Fighters'),
            new self(4, 2, 'finding'),
            new self(4, 2, 'lowmist'),
            new self(4, 2, 'passage'),
            new self(4, 3, 'DonkeyKong'),
            new self(4, 3, 'Skeleton'),
            new self(4, 4, 'BurningSkull'),
            new self(4, 4, 'orb'),
            new self(4, 4, 'Pigscene'),
            new self(4, 4, 'Pointer'),
            new self(4, 4, 'unpacked'),
        ] as $motive) {
            self::registerMotive($motive);
        }
    }

    public static function registerMotive(PaintingMotive $motive): void
    {
        if (!self::$initialized) {
            self::init();
        }

        self::$motives[$motive->getName()] = $motive;
    }

    public static function getMotiveByName(string $name): ?PaintingMotive
    {
        if (!self::$initialized) {
            self::init();
        }

        return self::$motives[$name] ?? null;
    }

    /**
     * @return array<string, PaintingMotive>
     */
    public static function getAll(): array
    {
        if (!self::$initialized) {
            self::init();
        }

        return self::$motives;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function __toString(): string
    {
        return 'PaintingMotive(name: ' . $this->name . ', height: ' . $this->height . ', width: ' . $this->width . ')';
    }
}
