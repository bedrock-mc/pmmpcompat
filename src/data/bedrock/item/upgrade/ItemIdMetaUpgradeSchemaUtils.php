<?php

declare(strict_types=1);

namespace pocketmine\data\bedrock\item\upgrade;

final class ItemIdMetaUpgradeSchemaUtils
{
    /** @return array<int, ItemIdMetaUpgradeSchema> */
    public static function loadSchemas(string $path, int $maxSchemaId): array
    {
        $result = [];
        $iterator = new \FilesystemIterator($path, \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::SKIP_DOTS);
        foreach ($iterator as $filename => $file) {
            if (!preg_match('/^(\d{4}).*\.json$/', (string) $filename, $matches)) {
                continue;
            }
            $schemaId = (int) $matches[1];
            if ($schemaId > $maxSchemaId) {
                continue;
            }
            $result[$schemaId] = self::loadSchemaFromString((string) file_get_contents($file->getPathname()), $schemaId);
        }
        ksort($result, SORT_NUMERIC);
        return $result;
    }

    public static function loadSchemaFromString(string $raw, int $schemaId): ItemIdMetaUpgradeSchema
    {
        try {
            $json = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
        }
        if (!is_array($json)) {
            throw new \RuntimeException('Unexpected root type of schema file');
        }
        return new ItemIdMetaUpgradeSchema(
            self::stringMap($json['renamedIds'] ?? []),
            self::metaMap($json['remappedMetas'] ?? []),
            $schemaId
        );
    }

    /** @return array<string, string> */
    private static function stringMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $key => $value) {
            $result[(string) $key] = (string) $value;
        }
        return $result;
    }

    /** @return array<string, array<int, string>> */
    private static function metaMap(mixed $input): array
    {
        $result = [];
        foreach ((array) $input as $id => $metas) {
            foreach ((array) $metas as $meta => $newId) {
                $result[(string) $id][(int) $meta] = (string) $newId;
            }
        }
        return $result;
    }
}
