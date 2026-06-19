<?php

declare(strict_types=1);

namespace pocketmine\math;

class RayTraceResult
{
    public function __construct(
        private Vector3 $hitVector,
        private int $hitFace = -1,
        private mixed $hitObject = null,
    ) {}

    public function getHitVector(): Vector3
    {
        return $this->hitVector;
    }

    public function getHitFace(): int
    {
        return $this->hitFace;
    }

    public function getHitObject(): mixed
    {
        return $this->hitObject;
    }
}
