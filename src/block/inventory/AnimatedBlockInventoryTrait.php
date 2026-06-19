<?php

declare(strict_types=1);

namespace pocketmine\block\inventory;

trait AnimatedBlockInventoryTrait
{
    public function getViewerCount(): int { return count($this->getViewers()); }
    public function getViewers(): array { return parent::getViewers(); }
    public function onClose(object $who): void { parent::onClose($who); }
    public function onOpen(object $who): void { parent::onOpen($who); }
}
