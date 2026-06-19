<?php

declare(strict_types=1);

namespace pocketmine\entity\object;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\FireworkRocketExplosion;
use pocketmine\nbt\tag\CompoundTag;

class FireworkRocket extends Entity implements \pocketmine\entity\NeverSavedWithChunkEntity
{
    private int $maxFlightTimeTicks = 0;
    /** @var FireworkRocketExplosion[] */
    private array $explosions = [];
    private bool $exploded = false;

    public function __construct(mixed ...$args)
    {
        $location = null;
        $nbt = null;
        foreach ($args as $arg) {
            if ($arg instanceof Location && $location === null) {
                $location = $arg;
            } elseif (is_int($arg)) {
                $this->setMaxFlightTimeTicks($arg);
            } elseif (is_array($arg)) {
                $this->setExplosions($arg);
            } elseif ($arg instanceof CompoundTag && $nbt === null) {
                $nbt = $arg;
            }
        }
        parent::__construct($location, $nbt);
    }

    public function canBeCollidedWith(mixed ...$args): bool { return false; }
    public function explode(mixed ...$args): void
    {
        $this->exploded = true;
        $this->flagForDespawn();
    }
    public function getExplosions(mixed ...$args): array { return $this->explosions; }
    public function getMaxFlightTimeTicks(mixed ...$args): int { return $this->maxFlightTimeTicks; }
    public static function getNetworkTypeId(mixed ...$args): string { return 'minecraft:fireworks_rocket'; }
    public function setExplosions(mixed ...$args): self
    {
        $explosions = is_array($args[0] ?? null) ? $args[0] : $args;
        foreach ($explosions as $explosion) {
            if (!$explosion instanceof FireworkRocketExplosion) {
                throw new \InvalidArgumentException('Explosions must contain FireworkRocketExplosion values');
            }
        }
        $this->explosions = array_values($explosions);
        return $this;
    }
    public function setMaxFlightTimeTicks(mixed ...$args): self
    {
        $ticks = (int) ($args[0] ?? 0);
        if ($ticks < 0) {
            throw new \InvalidArgumentException('Max flight time ticks cannot be negative');
        }
        $this->maxFlightTimeTicks = $ticks;
        return $this;
    }
    public function hasExploded(): bool { return $this->exploded; }
}
