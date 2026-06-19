<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class SquidInkCloudAnimation implements Animation
{
    public function __construct(private mixed $entity = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'squid_ink_cloud', 'entity' => $this->entity]; }
}
