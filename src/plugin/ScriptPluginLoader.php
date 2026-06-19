<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\utils\Utils;

class ScriptPluginLoader
{
    public function canLoadPlugin(string $path): bool
    {
        return is_file($path) && str_ends_with(strtolower($path), '.php');
    }

    public function loadPlugin(string $file): void
    {
        include_once $file;
    }

    public function getPluginDescription(string $file): ?PluginDescription
    {
        $content = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($content === false) {
            return null;
        }

        $insideHeader = false;
        $docCommentLines = [];
        foreach ($content as $line) {
            if (!$insideHeader) {
                if (str_contains($line, '/**')) {
                    $insideHeader = true;
                } else {
                    continue;
                }
            }
            $docCommentLines[] = $line;
            if (str_contains($line, '*/')) {
                break;
            }
        }

        $data = Utils::parseDocComment(implode("\n", $docCommentLines));
        return $data === [] ? null : new PluginDescription($data);
    }

    public function getAccessProtocol(): string
    {
        return '';
    }
}
