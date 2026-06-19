<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class ArrowShakeAnimation implements Animation
{
    public function __construct(private mixed $entity = null, private int $duration = 0) {}
    public function encode(mixed ...$args): array { return ['type' => 'arrow_shake', 'entity' => $this->entity, 'duration' => $this->duration]; }
}
