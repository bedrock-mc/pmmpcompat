<?php

declare(strict_types=1);

namespace pocketmine\world\generator;

use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\World;

final class FlatGeneratorOptions
{
    /** @param array<int, int> $structure @param array<string, array<string, string>|true> $extraOptions */
    public function __construct(
        private array $structure,
        private int $biomeId,
        private array $extraOptions = [],
    ) {}

    /** @return array<int, int> */
    public function getStructure(): array { return $this->structure; }
    public function getBiomeId(): int { return $this->biomeId; }
    /** @return array<string, array<string, string>|true> */
    public function getExtraOptions(): array { return $this->extraOptions; }

    /** @return array<int, int> */
    public static function parseLayers(string $layers): array
    {
        if (trim($layers) === '') {
            return [];
        }
        $result = [];
        $y = 0;
        foreach (array_map('trim', explode(',', $layers, World::Y_MAX - World::Y_MIN)) as $line) {
            if (preg_match('#^(?:(\d+)[x|*])?(.+)$#', $line, $matches) !== 1) {
                throw new InvalidGeneratorOptionsException("Invalid preset layer \"$line\"");
            }
            $count = $matches[1] !== '' ? (int) $matches[1] : 1;
            if ($count < 1) {
                throw new InvalidGeneratorOptionsException("Invalid preset layer count in \"$line\"");
            }
            $stateId = self::parseBlockStateId($matches[2]);
            for ($cy = $y, $y += $count; $cy < $y; ++$cy) {
                $result[$cy] = $stateId;
            }
        }
        return $result;
    }

    public static function parsePreset(string $presetString): self
    {
        $preset = explode(';', $presetString, 4);
        $structure = self::parseLayers($preset[1] ?? '');
        $biomeId = (int) ($preset[2] ?? BiomeIds::PLAINS);
        $options = [];
        preg_match_all('#(([0-9a-z_]{1,})\(?([0-9a-z_ =:]{0,})\)?),?#', $preset[3] ?? '', $matches);
        foreach ($matches[2] as $i => $option) {
            $params = true;
            if ($matches[3][$i] !== '') {
                $params = [];
                foreach (explode(' ', $matches[3][$i]) as $part) {
                    $split = explode('=', $part, 3);
                    if (isset($split[1])) {
                        $params[$split[0]] = $split[1];
                    }
                }
            }
            $options[$option] = $params;
        }
        return new self($structure, $biomeId, $options);
    }

    private static function parseBlockStateId(string $name): int
    {
        $normalized = strtolower(trim($name));
        $normalized = str_starts_with($normalized, 'minecraft:') ? substr($normalized, 10) : $normalized;
        $block = match ($normalized) {
            '0', 'air' => VanillaBlocks::AIR(),
            '1', 'stone' => VanillaBlocks::STONE(),
            '2', 'grass', 'grass_block' => VanillaBlocks::GRASS(),
            '3', 'dirt' => VanillaBlocks::DIRT(),
            default => throw new InvalidGeneratorOptionsException("Unknown flat preset block \"$name\""),
        };
        return $block->getStateId();
    }
}
