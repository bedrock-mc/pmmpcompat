<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

use pocketmine\color\Color;
use pocketmine\utils\Utils;

class SignText
{
    public const LINE_COUNT = 4;

    /** @var array{0: string, 1: string, 2: string, 3: string} */
    private array $lines;
    private Color $baseColor;
    private bool $glowing;

    /** @param array<int, string>|null $lines */
    public function __construct(?array $lines = null, ?Color $baseColor = null, bool $glowing = false)
    {
        $this->lines = array_fill(0, self::LINE_COUNT, '');
        if ($lines !== null) {
            if (count($lines) > self::LINE_COUNT) {
                throw new \InvalidArgumentException('Expected at most 4 lines, got ' . count($lines));
            }
            foreach ($lines as $index => $line) {
                $this->checkLineIndex($index);
                Utils::checkUTF8($line);
                if (str_contains($line, "\n")) {
                    throw new \InvalidArgumentException('Line must not contain newlines');
                }
                $this->lines[$index] = $line;
            }
        }
        $this->baseColor = $baseColor ?? new Color(0, 0, 0);
        $this->glowing = $glowing;
    }

    public static function fromBlob(string $blob, ?Color $baseColor = null, bool $glowing = false): self
    {
        return new self(array_slice(array_pad(explode("\n", $blob, self::LINE_COUNT + 1), self::LINE_COUNT, ''), 0, self::LINE_COUNT), $baseColor, $glowing);
    }

    /** @return array{0: string, 1: string, 2: string, 3: string} */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getLine(int $index): string
    {
        $this->checkLineIndex($index);
        return $this->lines[$index];
    }

    public function getBaseColor(): Color
    {
        return $this->baseColor;
    }

    public function isGlowing(): bool
    {
        return $this->glowing;
    }

    private function checkLineIndex(int|string $index): void
    {
        if (!is_int($index)) {
            throw new \InvalidArgumentException('Index must be an integer');
        }
        if ($index < 0 || $index >= self::LINE_COUNT) {
            throw new \InvalidArgumentException('Line index is out of bounds');
        }
    }
}
