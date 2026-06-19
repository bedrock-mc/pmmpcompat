<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class TotemUseAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'totem_use', 'entity' => $this->entity]; }
}
