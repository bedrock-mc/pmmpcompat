<?php

declare(strict_types=1);

namespace pocketmine\data\java;

use pocketmine\player\GameMode;
use pocketmine\utils\SingletonTrait;

final class GameModeIdMap
{
    use SingletonTrait;

    /** @var array<int, GameMode> */
    private array $idToEnum = [];

    public function __construct()
    {
        $this->idToEnum = [
            0 => GameMode::SURVIVAL(),
            1 => GameMode::CREATIVE(),
            2 => GameMode::ADVENTURE(),
            3 => GameMode::SPECTATOR(),
        ];
    }

    public function fromId(int $id): ?GameMode
    {
        return $this->idToEnum[$id] ?? null;
    }

    public function toId(GameMode $type): int
    {
        foreach ($this->idToEnum as $id => $mode) {
            if ($type->equals($mode)) {
                return $id;
            }
        }
        throw new \InvalidArgumentException('Game mode ' . $type->getId() . ' does not have a mapped ID');
    }
}
