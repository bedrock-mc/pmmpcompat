<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\encryption;

class PrepareEncryptionTask
{
    private ?string $aesKey = null;
    private ?string $handshakeJwt = null;

    public function __construct(private mixed $clientPub, private \Closure $onCompletion) {}

    public function onRun(): void
    {
        $serverPrivateKey = openssl_pkey_new(['ec' => ['curve_name' => 'secp384r1']]);
        if ($serverPrivateKey === false) {
            throw new \RuntimeException('openssl_pkey_new() failed: ' . openssl_error_string());
        }
        $sharedSecret = EncryptionUtils::generateSharedSecret($serverPrivateKey, $this->clientPub);
        $salt = random_bytes(16);
        $this->aesKey = EncryptionUtils::generateKey($sharedSecret, $salt);
        $this->handshakeJwt = EncryptionUtils::generateServerHandshakeJwt($serverPrivateKey, $salt);
    }

    public function onCompletion(): void
    {
        if ($this->aesKey === null || $this->handshakeJwt === null) {
            $this->onRun();
        }
        ($this->onCompletion)($this->aesKey, $this->handshakeJwt);
    }
}
