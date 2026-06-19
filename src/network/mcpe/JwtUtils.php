<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe;

use function base64_decode;
use function base64_encode;
use function chr;
use function count;
use function explode;
use function hex2bin;
use function json_decode;
use function json_encode;
use function json_last_error_msg;
use function ltrim;
use function openssl_error_string;
use function openssl_pkey_get_details;
use function openssl_pkey_get_public;
use function openssl_sign;
use function openssl_verify;
use function preg_match;
use function rtrim;
use function sprintf;
use function str_replace;
use function str_repeat;
use function strlen;
use function strtr;
use const JSON_THROW_ON_ERROR;
use const OPENSSL_ALGO_SHA256;
use const OPENSSL_ALGO_SHA384;

final class JwtUtils
{
    public const BEDROCK_SIGNING_KEY_CURVE_NAME = 'secp384r1';

    /** @return array{string, string, string} */
    public static function split(string $jwt): array
    {
        $parts = explode('.', $jwt, 4);
        if (count($parts) !== 3) {
            throw new JwtException('Expected exactly 3 JWT parts delimited by a period');
        }
        return [$parts[0], $parts[1], $parts[2]];
    }

    /** @return array{array<string, mixed>, array<string, mixed>, string} */
    public static function parse(string $token): array
    {
        [$headerPart, $bodyPart, $signaturePart] = self::split($token);
        $header = json_decode(self::b64UrlDecode($headerPart), true);
        if (!is_array($header)) {
            throw new JwtException('Failed to decode JWT header JSON: ' . json_last_error_msg());
        }
        $body = json_decode(self::b64UrlDecode($bodyPart), true);
        if (!is_array($body)) {
            throw new JwtException('Failed to decode JWT payload JSON: ' . json_last_error_msg());
        }
        return [$header, $body, self::b64UrlDecode($signaturePart)];
    }

    public static function verify(string $jwt, string $signingKeyDer, bool $ec): bool
    {
        [$header, $body, $signature] = self::split($jwt);
        $algorithm = $ec ? OPENSSL_ALGO_SHA384 : OPENSSL_ALGO_SHA256;
        $verified = openssl_verify($header . '.' . $body, self::b64UrlDecode($signature), self::derPublicKeyToPem($signingKeyDer), $algorithm);
        if ($verified === 0) {
            return false;
        }
        if ($verified === 1) {
            return true;
        }
        throw new JwtException('Error verifying JWT signature: ' . openssl_error_string());
    }

    /** @param array<string, mixed> $header @param array<string, mixed> $claims */
    public static function create(array $header, array $claims, \OpenSSLAsymmetricKey $signingKey): string
    {
        $body = self::b64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)) . '.' . self::b64UrlEncode(json_encode($claims, JSON_THROW_ON_ERROR));
        if (!openssl_sign($body, $signature, $signingKey, OPENSSL_ALGO_SHA384)) {
            throw new JwtException('Error signing JWT: ' . openssl_error_string());
        }
        return $body . '.' . self::b64UrlEncode($signature);
    }

    public static function b64UrlEncode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    public static function b64UrlDecode(string $str): string
    {
        $padding = strlen($str) % 4;
        if ($padding !== 0) {
            $str .= str_repeat('=', 4 - $padding);
        }
        $decoded = base64_decode(strtr($str, '-_', '+/'), true);
        if ($decoded === false) {
            throw new JwtException('Malformed base64url encoded payload could not be decoded');
        }
        return $decoded;
    }

    public static function emitDerPublicKey(\OpenSSLAsymmetricKey $opensslKey): string
    {
        $details = openssl_pkey_get_details($opensslKey);
        if ($details === false || !isset($details['key'])) {
            throw new JwtException('Failed to get details from OpenSSL key resource');
        }
        if (preg_match("@^-----BEGIN[A-Z\d ]+PUBLIC KEY-----\n([A-Za-z\d+/\n]+)\n-----END[A-Z\d ]+PUBLIC KEY-----\n$@", $details['key'], $matches) === 1) {
            $derKey = base64_decode(str_replace("\n", '', $matches[1]), true);
            if ($derKey !== false) {
                return $derKey;
            }
        }
        throw new JwtException('OpenSSL resource contains invalid public key');
    }

    public static function parseDerPublicKey(string $derKey): \OpenSSLAsymmetricKey
    {
        $key = openssl_pkey_get_public(self::derPublicKeyToPem($derKey));
        if ($key === false) {
            throw new JwtException('OpenSSL failed to parse key: ' . openssl_error_string());
        }
        return $key;
    }

    public static function derPublicKeyToPem(string $derKey): string
    {
        return sprintf("-----BEGIN PUBLIC KEY-----\n%s\n-----END PUBLIC KEY-----\n", base64_encode($derKey));
    }

    public static function rsaPublicKeyModExpToDer(string $nBase64, string $eBase64): string
    {
        $modulus = self::encodeDerBytes(2, self::b64UrlDecode($nBase64));
        $publicExponent = self::encodeDerBytes(2, self::b64UrlDecode($eBase64));
        $rsaPublicKey = self::encodeDerBytes(48, $modulus . $publicExponent);
        $rsaOID = hex2bin('300d06092a864886f70d0101010500');
        if ($rsaOID === false) {
            throw new JwtException('Failed to decode RSA OID');
        }
        return self::encodeDerBytes(48, $rsaOID . self::encodeDerBytes(3, chr(0) . $rsaPublicKey));
    }

    private static function encodeDerLength(int $length): string
    {
        if ($length <= 0x7f) {
            return chr($length);
        }
        $bytes = '';
        while ($length > 0) {
            $bytes = chr($length & 0xff) . $bytes;
            $length >>= 8;
        }
        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    private static function encodeDerBytes(int $tag, string $data): string
    {
        return chr($tag) . self::encodeDerLength(strlen($data)) . $data;
    }
}
