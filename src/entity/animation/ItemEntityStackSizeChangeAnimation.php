<?php

declare(strict_types=1);

namespace pocketmine\entity\animation;

class ItemEntityStackSizeChangeAnimation implements Animation
{
    public function __construct(private mixed $entity = null, private int $newStackSize = 0) {}
    public function encode(mixed ...$args): array { return ['type' => 'item_stack_size_change', 'entity' => $this->entity, 'new_stack_size' => $this->newStackSize]; }
}
