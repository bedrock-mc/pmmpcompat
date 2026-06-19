<?php

declare(strict_types=1);

namespace pocketmine\command;

use pocketmine\command\utils\CommandStringHelper;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\utils\TextFormat;

class FormattedCommandAlias extends Command
{
    private const FORMAT_STRING_REGEX = '/\G\$(\$)?((?!0)+\d+)(-)?/';

    /** @param list<string> $formatStrings */
    public function __construct(string $alias, private array $formatStrings)
    {
        parent::__construct($alias, 'User-defined command alias');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool
    {
        $commands = [];
        foreach ($this->formatStrings as $formatString) {
            try {
                $formatArgs = CommandStringHelper::parseQuoteAware((string) $formatString);
                $unresolved = [];
                $processedArgs = [];
                foreach ($formatArgs as $formatArg) {
                    $processedArg = $this->buildCommand($formatArg, $args);
                    if ($processedArg === null) {
                        $unresolved[] = $formatArg;
                    } elseif ($unresolved !== []) {
                        throw new \InvalidArgumentException('Unable to resolve format arguments (' . implode(', ', $unresolved) . ') in command string "' . $formatString . '" due to missing arguments');
                    } else {
                        $processedArgs[] = $processedArg;
                    }
                }
                $commands[] = $processedArgs;
            } catch (\InvalidArgumentException $e) {
                $sender->sendMessage(TextFormat::RED . $e->getMessage());
                return false;
            }
        }

        $result = true;
        foreach ($commands as $commandArgs) {
            $targetLabel = array_shift($commandArgs);
            if ($targetLabel === null) {
                throw new \LogicException('Alias format produced no command label');
            }
            $target = $sender->getServer()->getCommandMap()->getCommand($targetLabel);
            if ($target === null) {
                $sender->sendMessage(TextFormat::RED . 'Unknown command: ' . $targetLabel);
                $result = false;
                continue;
            }
            try {
                $result = $target->execute($sender, $targetLabel, $commandArgs) && $result;
            } catch (InvalidCommandSyntaxException) {
                $sender->sendMessage($target->getUsage());
                $result = false;
            }
        }
        return $result;
    }

    /** @param list<string> $args */
    private function buildCommand(string $formatString, array $args): ?string
    {
        $index = 0;
        while (($index = strpos($formatString, '$', $index)) !== false) {
            $start = $index;
            if ($index > 0 && $formatString[$start - 1] === '\\') {
                $formatString = substr($formatString, 0, $start - 1) . substr($formatString, $start);
                continue;
            }

            $info = self::extractPlaceholderInfo($formatString, $index);
            if ($info === null) {
                throw new \InvalidArgumentException('Invalid replacement token');
            }
            [$fullPlaceholder, $required, $position, $rest] = $info;
            --$position;
            if ($required && $position >= count($args)) {
                throw new \InvalidArgumentException('Missing required argument ' . ($position + 1));
            }

            $replacement = self::buildReplacement($args, $position, $rest);
            if ($replacement === null) {
                return null;
            }

            $formatString = substr($formatString, 0, $start) . $replacement . substr($formatString, $index + strlen($fullPlaceholder));
            $index = $start + strlen($replacement);
        }
        return $formatString;
    }

    /** @param list<string> $args */
    private static function buildReplacement(array $args, int $position, bool $rest): ?string
    {
        if ($rest && $position < count($args)) {
            return implode(' ', array_slice($args, $position));
        }
        return $position < count($args) ? $args[$position] : null;
    }

    /** @return array{string, bool, int, bool}|null */
    private static function extractPlaceholderInfo(string $commandString, int $offset): ?array
    {
        if (preg_match(self::FORMAT_STRING_REGEX, $commandString, $matches, 0, $offset) !== 1) {
            return null;
        }
        return [$matches[0], ($matches[1] ?? '') !== '', (int) $matches[2], ($matches[3] ?? '') !== ''];
    }
}
