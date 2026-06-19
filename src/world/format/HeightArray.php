<?php

declare(strict_types=1);

namespace pocketmine\world\format;

final class HeightArray
{
    /** @var \SplFixedArray<int> */
    private \SplFixedArray $array;

    /**
     * @param int[] $values ZZZZXXXX key bit order
     */
    public function __construct(array $values)
    {
        if (count($values) !== 256) {
            throw new \InvalidArgumentException('Expected exactly 256 values');
        }
        $this->array = \SplFixedArray::fromArray(array_values($values));
    }

    public static function fill(int $value): self
    {
        return new self(array_fill(0, 256, $value));
    }

    private static function idx(int $x, int $z): int
    {
        if ($x < 0 || $x >= 16 || $z < 0 || $z >= 16) {
            throw new \InvalidArgumentException('x and z must be in the range 0-15');
        }
        return ($z << 4) | $x;
    }

    public function get(int $x, int $z): int
    {
        return $this->array[self::idx($x, $z)];
    }

    public function set(int $x, int $z, int $height): void
    {
        $this->array[self::idx($x, $z)] = $height;
    }

    /**
     * @return int[] ZZZZXXXX key bit order
     */
    public function getValues(): array
    {
        return $this->array->toArray();
    }

    public function __clone()
    {
        $this->array = clone $this->array;
    }
}
