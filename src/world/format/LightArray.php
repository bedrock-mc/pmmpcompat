<?php

declare(strict_types=1);

namespace pocketmine\world\format;

final class LightArray
{
    /** @var int[] */
    private array $values;

    /**
     * @param int[]|string $values 4096 nibble values, or 2048 packed nibbles.
     */
    public function __construct(array|string $values)
    {
        if (is_string($values)) {
            if (strlen($values) !== 2048) {
                throw new \InvalidArgumentException('Expected exactly 2048 packed light bytes');
            }
            $unpacked = [];
            for ($i = 0; $i < 2048; ++$i) {
                $byte = ord($values[$i]);
                $unpacked[] = $byte & 0x0f;
                $unpacked[] = ($byte >> 4) & 0x0f;
            }
            $values = $unpacked;
        }
        if (count($values) !== 4096) {
            throw new \InvalidArgumentException('Expected exactly 4096 light values');
        }
        $this->values = array_map(self::clamp(...), array_values($values));
    }

    public static function fill(int $value): self
    {
        return new self(array_fill(0, 4096, self::clamp($value)));
    }

    private static function clamp(int $value): int
    {
        return max(0, min(15, $value));
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

    public function set(int $x, int $y, int $z, int $value): void
    {
        $this->values[self::idx($x, $y, $z)] = self::clamp($value);
    }

    public function isUniform(int $value): bool
    {
        $value = self::clamp($value);
        foreach ($this->values as $entry) {
            if ($entry !== $value) {
                return false;
            }
        }
        return true;
    }

    public function getData(): string
    {
        $packed = '';
        for ($i = 0; $i < 4096; $i += 2) {
            $packed .= chr($this->values[$i] | ($this->values[$i + 1] << 4));
        }
        return $packed;
    }
}
