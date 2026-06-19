<?php

declare(strict_types=1);

namespace pocketmine\event\world;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\math\Vector3;
use pocketmine\world\sound\Sound;
use pocketmine\world\World;

class WorldSoundEvent extends WorldEvent implements Cancellable
{
    use CancellableTrait;

    public function __construct(World $world, private Sound $sound, private Vector3 $position, private array $recipients)
    {
        parent::__construct($world);
    }

    public function getSound(): Sound { return $this->sound; }
    public function setSound(Sound $sound): void { $this->sound = $sound; }
    public function getPosition(): Vector3 { return $this->position; }
    public function getRecipients(): array { return $this->recipients; }
    public function setRecipients(array $recipients): void { $this->recipients = $recipients; }
}
