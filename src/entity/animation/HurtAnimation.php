<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class HurtAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'hurt', 'entity' => $this->entity]; }
}
