<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

class ProcessLegacyLoginTask
{
    public const LEGACY_MOJANG_ROOT_PUBLIC_KEY = '';

    private mixed $result = null;

    public function __construct(
        private string $jwt = '',
        private ?string $expectedKeyDer = null,
        private mixed $callback = null,
    ) {
    }

    public function onRun(): void
    {
        $this->result = AuthJwtHelper::validateLegacyAuthToken($this->jwt, $this->expectedKeyDer);
    }

    public function onCompletion(): void
    {
        if (is_callable($this->callback)) {
            ($this->callback)($this->result);
        }
    }
}
