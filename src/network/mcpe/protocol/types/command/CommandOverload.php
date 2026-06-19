<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol\types\command;

final class CommandOverload
{
    /** @param CommandParameter[] $parameters */
    public function __construct(private bool $chaining, private array $parameters) {}

    /** @return CommandParameter[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function isChaining(): bool
    {
        return $this->chaining;
    }
}
