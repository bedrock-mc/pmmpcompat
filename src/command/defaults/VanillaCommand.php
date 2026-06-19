<?php

declare(strict_types=1);

namespace pocketmine\command\defaults;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class VanillaCommand extends Command
{
    public const MAX_COORD = 30000000;
    public const MIN_COORD = -30000000;

    public function __construct(string $name, string $description = '', string $usageMessage = '', array $aliases = [])
    {
        parent::__construct($name, $description, $aliases);
        if ($usageMessage !== '') {
            $this->setUsage($usageMessage);
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        $sender->sendMessage('/' . $commandLabel . ' is handled by the Dragonfly host in pmmpcompat.');
        return true;
    }

    protected static function commandNameFromClass(string $class): string
    {
        $short = substr(strrchr($class, '\\') ?: $class, 1) ?: $class;
        return strtolower(preg_replace('/Command$/', '', $short) ?? $short);
    }
}
