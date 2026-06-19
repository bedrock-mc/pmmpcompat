<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\command;

final class CommandParameter
{
    public string $paramName = '';
    public int $paramType = 0;
    public bool $isOptional = false;
    public ?CommandEnum $enum = null;
}
