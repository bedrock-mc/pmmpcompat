<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class FireworkParticlesAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'firework_particles', 'entity' => $this->entity]; }
}
