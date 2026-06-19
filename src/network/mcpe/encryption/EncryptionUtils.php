<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\encryption;

class EncryptionUtils
{
    public static function generateSharedSecret(mixed $privateKey, mixed $publicKey): string
    {
        if (!function_exists('openssl_pkey_derive')) {
            throw new \RuntimeException('OpenSSL key derivation is not available');
        }
        $secret = openssl_pkey_derive($publicKey, $privateKey, 32);
        if ($secret === false) {
            throw new \RuntimeException('OpenSSL shared secret derivation failed: ' . openssl_error_string());
        }
        return $secret;
    }

    public static function generateKey(string $sharedSecret, string $salt): string
    {
        return hash('sha256', $salt . $sharedSecret, true);
    }

    public static function generateServerHandshakeJwt(mixed $serverPrivateKey, string $salt): string
    {
        $header = self::base64UrlEncode(json_encode(['alg' => 'ES384', 'x5u' => 'pmmpcompat'], JSON_THROW_ON_ERROR));
        $payload = self::base64UrlEncode(json_encode(['salt' => base64_encode($salt)], JSON_THROW_ON_ERROR));
        $body = $header . '.' . $payload;
        $signature = '';
        if (!openssl_sign($body, $signature, $serverPrivateKey, OPENSSL_ALGO_SHA384)) {
            throw new \RuntimeException('OpenSSL signing failed: ' . openssl_error_string());
        }
        return $body . '.' . self::base64UrlEncode($signature);
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
