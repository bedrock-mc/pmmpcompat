<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\encryption;

class EncryptionContext
{
    private const CHECKSUM_ALGO = 'sha256';
    public static bool $ENABLED = true;
    private int $decryptCounter = 0;
    private int $encryptCounter = 0;

    public function __construct(
        private string $encryptionKey,
        private string $algorithm = 'AES-256-CTR',
        private ?string $iv = null
    ) {
        $this->iv ??= substr($encryptionKey, 0, 12) . "\x00\x00\x00\x02";
    }

    public static function fakeGCM(string $encryptionKey): self
    {
        return new self($encryptionKey, 'AES-256-CTR', substr($encryptionKey, 0, 12) . "\x00\x00\x00\x02");
    }

    public static function cfb8(string $encryptionKey): self
    {
        return new self($encryptionKey, 'AES-256-CFB8', substr($encryptionKey, 0, 16));
    }

    public function decrypt(string $encrypted): string
    {
        if (strlen($encrypted) < 9) {
            throw new DecryptionException('Payload is too short');
        }
        $decrypted = $this->crypt($encrypted, false);
        $payload = substr($decrypted, 0, -8);
        $actual = substr($decrypted, -8);
        $expected = $this->calculateChecksum($this->decryptCounter++, $payload);
        if (!hash_equals($expected, $actual)) {
            throw new DecryptionException('Encrypted packet has invalid checksum');
        }
        return $payload;
    }

    public function encrypt(string $payload): string
    {
        return $this->crypt($payload . $this->calculateChecksum($this->encryptCounter++, $payload), true);
    }

    private function crypt(string $payload, bool $encrypt): string
    {
        $result = $encrypt ? openssl_encrypt(
            $payload,
            $this->algorithm,
            $this->encryptionKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->iv
        ) : openssl_decrypt(
            $payload,
            $this->algorithm,
            $this->encryptionKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->iv
        );
        if ($result === false) {
            throw new \RuntimeException('OpenSSL encryption failed: ' . openssl_error_string());
        }
        return $result;
    }

    private function calculateChecksum(int $counter, string $payload): string
    {
        $hash = hash(self::CHECKSUM_ALGO, pack('V2', $counter & 0xffffffff, ($counter >> 32) & 0xffffffff) . $payload . $this->encryptionKey, true);
        return substr($hash, 0, 8);
    }
}
