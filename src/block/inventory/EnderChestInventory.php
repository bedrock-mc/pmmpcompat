<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

class EnderChestInventory extends CompatBlockInventory
{
    public function __construct(mixed ...$args) { parent::__construct($args[0] ?? null, 27); }
    public function getEnderInventory(mixed ...$args): mixed { return $this; }
    public function getViewerCount(): int { return parent::getViewerCount(); }
    public function onClose(object $who): void { parent::onClose($who); }
}
