<?php

declare(strict_types=1);

namespace pocketmine\color;

class Color
{
    public function __construct(
        private int $r,
        private int $g,
        private int $b,
        private int $a = 0xff,
    ) {
        foreach (['r' => $r, 'g' => $g, 'b' => $b, 'a' => $a] as $name => $value) {
            if ($value < 0 || $value > 255) {
                throw new \InvalidArgumentException($name . ' must be in range 0 ... 255');
            }
        }
    }

    public function getR(): int { return $this->r; }
    public function getG(): int { return $this->g; }
    public function getB(): int { return $this->b; }
    public function getA(): int { return $this->a; }

    public function toARGB(): int
    {
        return (($this->a & 0xff) << 24) | (($this->r & 0xff) << 16) | (($this->g & 0xff) << 8) | ($this->b & 0xff);
    }
}
