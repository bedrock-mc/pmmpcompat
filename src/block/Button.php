<?php

declare(strict_types=1);

namespace pocketmine\block;

class Button extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:button', 'Button'); }
    public function isPressed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onNearbyBlockChange(): void { $this->compatMethod(__FUNCTION__, []); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function place(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function setPressed(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
