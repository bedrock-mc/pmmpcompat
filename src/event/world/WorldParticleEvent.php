<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;
use pocketmine\world\particle\Particle;
use pocketmine\world\World;

class WorldParticleEvent extends WorldEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(World $world, private Particle $particle, private Vector3 $position, private array $recipients)
    {
        parent::__construct($world);
    }

    public function getParticle(): Particle { return $this->particle; }
    public function setParticle(Particle $particle): void { $this->particle = $particle; }
    public function getPosition(): Vector3 { return $this->position; }
    public function getRecipients(): array { return $this->recipients; }
    public function setRecipients(array $recipients): void { $this->recipients = $recipients; }
}
