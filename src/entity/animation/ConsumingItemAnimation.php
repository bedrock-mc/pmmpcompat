<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class ConsumingItemAnimation implements Animation
{
    public function __construct(private mixed $entity = null, private mixed $item = null) {}
    public function encode(mixed ...$args): array { return ['type' => 'consuming_item', 'entity' => $this->entity, 'item' => $this->item]; }
}
