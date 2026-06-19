<?php

declare(strict_types=1);

namespace pocketmine\block\tile;

interface Nameable
{
    public const TAG_CUSTOM_NAME = 0;

    public function getDefaultName(mixed ...$args): mixed;
    public function getName(mixed ...$args): mixed;
    public function hasName(mixed ...$args): mixed;
    public function setName(mixed ...$args): mixed;
}
