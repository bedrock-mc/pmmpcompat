<?php

declare(strict_types=1);

namespace pocketmine\resourcepacks\json;

final class Manifest
{
    public int $format_version = 2;
    public ManifestHeader $header;
    /** @var ManifestModuleEntry[] */
    public array $modules = [];
    public ?ManifestMetadata $metadata = null;
    /** @var string[]|null */
    public ?array $capabilities = null;
    /** @var ManifestDependencyEntry[]|null */
    public ?array $dependencies = null;

    public function __construct()
    {
        $this->header = new ManifestHeader();
    }

    public static function fromArray(array $data): self
    {
        $manifest = new self();
        $manifest->format_version = (int) ($data['format_version'] ?? 2);
        $header = $data['header'] ?? [];
        if (!is_array($header)) {
            throw new \InvalidArgumentException('manifest header must be an object');
        }
        $manifest->header->description = (string) ($header['description'] ?? '');
        $manifest->header->name = (string) ($header['name'] ?? '');
        $manifest->header->uuid = (string) ($header['uuid'] ?? '');
        $manifest->header->version = array_map('intval', $header['version'] ?? [0, 0, 0]);
        $manifest->header->min_engine_version = array_map('intval', $header['min_engine_version'] ?? [1, 0, 0]);
        foreach (($data['modules'] ?? []) as $moduleData) {
            if (!is_array($moduleData)) {
                continue;
            }
            $module = new ManifestModuleEntry();
            $module->description = (string) ($moduleData['description'] ?? '');
            $module->type = (string) ($moduleData['type'] ?? '');
            $module->uuid = (string) ($moduleData['uuid'] ?? '');
            $module->version = array_map('intval', $moduleData['version'] ?? [0, 0, 0]);
            $manifest->modules[] = $module;
        }
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $manifest->metadata = new ManifestMetadata();
            $manifest->metadata->authors = isset($data['metadata']['authors']) && is_array($data['metadata']['authors']) ? array_map('strval', $data['metadata']['authors']) : null;
            $manifest->metadata->license = isset($data['metadata']['license']) ? (string) $data['metadata']['license'] : null;
            $manifest->metadata->url = isset($data['metadata']['url']) ? (string) $data['metadata']['url'] : null;
        }
        $manifest->capabilities = isset($data['capabilities']) && is_array($data['capabilities']) ? array_map('strval', $data['capabilities']) : null;
        if (isset($data['dependencies']) && is_array($data['dependencies'])) {
            $manifest->dependencies = [];
            foreach ($data['dependencies'] as $dependencyData) {
                if (!is_array($dependencyData)) {
                    continue;
                }
                $dependency = new ManifestDependencyEntry();
                $dependency->uuid = (string) ($dependencyData['uuid'] ?? '');
                $dependency->version = array_map('intval', $dependencyData['version'] ?? [0, 0, 0]);
                $manifest->dependencies[] = $dependency;
            }
        }
        if ($manifest->header->name === '' || $manifest->header->uuid === '' || $manifest->modules === []) {
            throw new \InvalidArgumentException('manifest header name, header uuid and modules are required');
        }
        return $manifest;
    }
}
