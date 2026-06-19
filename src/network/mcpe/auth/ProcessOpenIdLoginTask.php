<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

class ProcessOpenIdLoginTask
{
    public const MOJANG_AUDIENCE = AuthJwtHelper::MOJANG_AUDIENCE;

    private mixed $result = null;

    public function __construct(
        private string $jwt = '',
        private string $signingKeyDer = '',
        private string $issuer = '',
        private string $audience = self::MOJANG_AUDIENCE,
        private mixed $callback = null,
    ) {
    }

    public function onRun(): void
    {
        $this->result = AuthJwtHelper::validateOpenIdAuthToken($this->jwt, $this->signingKeyDer, $this->issuer, $this->audience);
    }

    public function onCompletion(): void
    {
        if (is_callable($this->callback)) {
            ($this->callback)($this->result);
        }
    }
}
