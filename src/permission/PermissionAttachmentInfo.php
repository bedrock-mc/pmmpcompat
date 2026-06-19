<?php

declare(strict_types=1);

namespace pocketmine\permission;

class PermissionAttachmentInfo
{
    public function __construct(
        private string $permission,
        private ?PermissionAttachment $attachment,
        private bool $value,
        private ?PermissionAttachmentInfo $groupPermission,
    ) {}

    public function getPermission(): string { return $this->permission; }
    public function getAttachment(): ?PermissionAttachment { return $this->attachment; }
    public function getValue(): bool { return $this->value; }
    public function getGroupPermissionInfo(): ?PermissionAttachmentInfo { return $this->groupPermission; }
}
