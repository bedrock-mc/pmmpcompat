<?php

declare(strict_types=1);

namespace pocketmine\plugin;

final class PluginDescriptionCommandEntry
{
    /** @param string[] $aliases */
    public function __construct(
        private ?string $description,
        private ?string $usageMessage,
        private array $aliases,
        private string $permission,
        private ?string $permissionDeniedMessage,
    ) {}

    public static function fromArray(array $data): self
    {
        $aliases = $data['aliases'] ?? [];
        if (is_string($aliases)) {
            $aliases = [$aliases];
        }
        return new self(
            isset($data['description']) ? (string) $data['description'] : null,
            isset($data['usage']) ? (string) $data['usage'] : null,
            is_array($aliases) ? array_map('strval', $aliases) : [],
            isset($data['permission']) ? (string) $data['permission'] : '',
            isset($data['permission-message']) ? (string) $data['permission-message'] : (isset($data['permission_message']) ? (string) $data['permission_message'] : null),
        );
    }

    public function getDescription(): ?string { return $this->description; }
    public function getUsageMessage(): ?string { return $this->usageMessage; }
    /** @return string[] */
    public function getAliases(): array { return $this->aliases; }
    public function getPermission(): string { return $this->permission; }
    public function getPermissionDeniedMessage(): ?string { return $this->permissionDeniedMessage; }
}
