<?php

declare(strict_types=1);

namespace pocketmine\entity;

final class Skin
{
    public const ACCEPTED_SKIN_SIZES = [64 * 32 * 4, 64 * 64 * 4, 128 * 128 * 4];

    public function __construct(
        private string $skinId = 'pmmpcompat:default',
        private string $skinData = '',
        private string $capeData = '',
        private string $geometryName = '',
        private string $geometryData = '',
    ) {
        if ($this->skinId === '') {
            throw new InvalidSkinException('Skin ID must not be empty');
        }
        if ($this->skinData !== '' && !in_array(strlen($this->skinData), self::ACCEPTED_SKIN_SIZES, true)) {
            throw new InvalidSkinException('Invalid skin data size ' . strlen($this->skinData) . ' bytes');
        }
        if ($this->capeData !== '' && strlen($this->capeData) !== 8192) {
            throw new InvalidSkinException('Invalid cape data size ' . strlen($this->capeData) . ' bytes');
        }
    }

    public function getSkinId(): string { return $this->skinId; }
    public function getSkinData(): string { return $this->skinData; }
    public function getCapeData(): string { return $this->capeData; }
    public function getGeometryName(): string { return $this->geometryName; }
    public function getGeometryData(): string { return $this->geometryData; }
}
