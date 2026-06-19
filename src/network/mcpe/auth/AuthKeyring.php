<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

final class AuthKeyring
{
    /** @param array<string, string> $keys */
    public function __construct(
        private string $issuer,
        private array $keys,
    ) {
    }

    public function getIssuer(): string
    {
        return $this->issuer;
    }

    public function getKey(string $keyId): ?string
    {
        return $this->keys[$keyId] ?? null;
    }
}
