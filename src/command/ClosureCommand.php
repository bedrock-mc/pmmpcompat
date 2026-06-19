<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\lang\Translatable;

final class ClosureCommand extends Command
{
    private \Closure $execute;

    /** @param list<string> $permissions */
    public function __construct(
        string $name,
        \Closure $execute,
        array $permissions,
        Translatable|string $description = '',
        Translatable|string|null $usageMessage = null,
        array $aliases = []
    ) {
        $this->execute = $execute;
        parent::__construct($name, self::stringify($description), array_map('strval', $aliases));
        $this->setPermissions($permissions);
        if ($usageMessage !== null) {
            $this->setUsage(self::stringify($usageMessage));
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        return (bool) ($this->execute)($sender, $this, $commandLabel, $args);
    }

    private static function stringify(Translatable|string $value): string
    {
        return $value instanceof Translatable ? $value->getText() : $value;
    }
}
