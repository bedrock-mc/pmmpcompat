<?php

declare(strict_types=1);

namespace pocketmine;

final class MemoryDump
{
    private function __construct() {}

    public static function dumpMemory(mixed $startingObject, string $outputFolder, int $maxNesting, int $maxStringSize, mixed $logger): void
    {
        if (!is_dir($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        $summary = [
            'type' => get_debug_type($startingObject),
            'maxNesting' => $maxNesting,
            'maxStringSize' => $maxStringSize,
            'memoryUsage' => memory_get_usage(),
            'memoryUsageReal' => memory_get_usage(true),
        ];
        file_put_contents($outputFolder . DIRECTORY_SEPARATOR . 'serverEntry.js', json_encode($summary, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
        file_put_contents($outputFolder . DIRECTORY_SEPARATOR . 'objects.js', '');
        file_put_contents($outputFolder . DIRECTORY_SEPARATOR . 'referenceCounts.js', json_encode([], JSON_THROW_ON_ERROR));
        file_put_contents($outputFolder . DIRECTORY_SEPARATOR . 'instanceCounts.js', json_encode([], JSON_THROW_ON_ERROR));
        if (is_object($logger) && method_exists($logger, 'info')) {
            $logger->info('Finished!');
        }
    }
}
