<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

use pocketmine\lang\Translatable;

class VerifyLoginException extends \RuntimeException
{
    private Translatable|string $disconnectMessage;

    public function __construct(string $message, Translatable|string|null $disconnectMessage = null, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->disconnectMessage = $disconnectMessage ?? $message;
    }

    public function getDisconnectMessage(): Translatable|string
    {
        return $this->disconnectMessage;
    }
}
