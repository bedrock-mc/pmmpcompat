<?php

declare(strict_types=1);

namespace pocketmine\entity;

final class EntitySizeInfo
{
    private float $eyeHeight;

    public function __construct(private float $height, private float $width, ?float $eyeHeight = null)
    {
        $this->eyeHeight = $eyeHeight ?? min($height / 2 + 0.1, $height);
    }

    public function getHeight(): float { return $this->height; }
    public function getWidth(): float { return $this->width; }
    public function getEyeHeight(): float { return $this->eyeHeight; }
    public function scale(float $newScale): self { return new self($this->height * $newScale, $this->width * $newScale, $this->eyeHeight * $newScale); }
}
