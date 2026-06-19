<?php

declare(strict_types=1);

namespace pocketmine\event\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\item\Item;

class EntityShootBowEvent extends EntityEvent implements Cancellable
{
    use CancellableTrait;

    private Entity $projectile;

    public function __construct(
        Living $shooter,
        private Item $bow,
        Projectile $projectile,
        private float $force,
    ) {
        $this->entity = $shooter;
        $this->projectile = $projectile;
    }

    public function getEntity(): Living { return $this->entity; }
    public function getBow(): Item { return $this->bow; }
    public function getProjectile(): Entity { return $this->projectile; }
    public function setProjectile(Entity $projectile): void { $this->projectile = $projectile; }
    public function getForce(): float { return $this->force; }
    public function setForce(float $force): void { $this->force = $force; }
}
