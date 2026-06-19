<?php

declare(strict_types=1);

namespace pocketmine\block\utils;

trait MultiAnyFacingTrait
{
    public function getFaces(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function hasFace(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFace(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
    public function setFaces(mixed ...$args): mixed { return $this->compatMethod(__FUNCTION__, $args); }
}
