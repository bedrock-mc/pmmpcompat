<?php

declare(strict_types=1);

namespace pocketmine\block;

class Lectern extends \pocketmine\block\Block
{
    public function __construct(mixed ...$args) { parent::__construct('minecraft:lectern', 'Lectern'); }
    public function getBook(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getDrops(\pocketmine\item\Item $item): array { return $this->compatMethod(__FUNCTION__, [$item]); }
    public function getFlammability(): int { return $this->compatMethod(__FUNCTION__, []); }
    public function getSupportType(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function getViewedPage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function isProducingSignal(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onAttack(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onInteract(mixed ...$args): bool { return $this->compatMethod(__FUNCTION__, $args); }
    public function onPageTurn(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function onScheduledUpdate(): void { $this->compatMethod(__FUNCTION__, []); }
    public function readStateFromWorld(): \pocketmine\block\Block { return $this->compatMethod(__FUNCTION__, []); }
    public function setBook(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setProducingSignal(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setViewedPage(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function writeStateToWorld(): void { $this->compatMethod(__FUNCTION__, []); }
}
