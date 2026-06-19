<?php

declare(strict_types=1);

namespace pocketmine\world\format;

final class PalettedBlockArray
{
    /** @var int[] */
    private array $values;

    public function __construct(private int $defaultStateId)
    {
        $this->values = array_fill(0, 4096, $defaultStateId);
    }

    public static function fromData(int $bitsPerBlock, string $words, array $palette): self
    {
        $array = new self((int) ($palette[0] ?? 0));
        return $array;
    }

    public static function getExpectedWordArraySize(int $bitsPerBlock): int
    {
        return 0;
    }

    private static function idx(int $x, int $y, int $z): int
    {
        if ($x < 0 || $x >= 16 || $y < 0 || $y >= 16 || $z < 0 || $z >= 16) {
            throw new \InvalidArgumentException('x, y and z must be in the range 0-15');
        }
        return ($y << 8) | ($z << 4) | $x;
    }

    public function get(int $x, int $y, int $z): int
    {
        return $this->values[self::idx($x, $y, $z)];
    }

    public function set(int $x, int $y, int $z, int $stateId): void
    {
        $this->values[self::idx($x, $y, $z)] = $stateId;
    }

    public function getBitsPerBlock(): int
    {
        return $this->isUniform($this->values[0]) ? 0 : 16;
    }

    public function collectGarbage(): void
    {
    }

    public function isUniform(int $value): bool
    {
        foreach ($this->values as $entry) {
            if ($entry !== $value) {
                return false;
            }
        }
        return true;
    }

    public function __clone()
    {
        $this->values = array_values($this->values);
    }
}
