<?php

declare(strict_types=1);

namespace pocketmine\world\format\io;

use pocketmine\math\Vector3;

interface WorldData
{
    public function getDifficulty(): int;
    public function getGenerator(): string;
    public function getGeneratorOptions(): string;
    public function getLightningLevel(): float;
    public function getLightningTime(): int;
    public function getName(): string;
    public function getRainLevel(): float;
    public function getRainTime(): int;
    public function getSeed(): int;
    public function getSpawn(): Vector3;
    public function getTime(): int;
    public function save(): void;
    public function setDifficulty(int $difficulty): void;
    public function setLightningLevel(float $level): void;
    public function setLightningTime(int $ticks): void;
    public function setName(string $value): void;
    public function setRainLevel(float $level): void;
    public function setRainTime(int $ticks): void;
    public function setSpawn(Vector3 $pos): void;
    public function setTime(int $value): void;
}
