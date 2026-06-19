<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class ArmSwingAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'arm_swing', 'entity' => $this->entity]; }
}
