<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class DeathAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'death', 'entity' => $this->entity]; }
}
