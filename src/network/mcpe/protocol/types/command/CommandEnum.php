<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\command;

final class CommandEnum
{
    /** @param string[] $values */
    public function __construct(public string $enumName, public array $values) {}
}
