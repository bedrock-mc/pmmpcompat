<?php

declare(strict_types=1);

namespace pocketmine\event;

final class RegisteredListenerCache
{
    /** @var list<RegisteredListener>|null */
    public ?array $list = null;
}
