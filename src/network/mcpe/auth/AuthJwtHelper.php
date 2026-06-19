<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

use pocketmine\lang\KnownTranslationFactory;
use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use function base64_decode;
use function is_array;
use function time;

final class AuthJwtHelper
{
    public const MOJANG_AUDIENCE = 'api://auth-minecraft-services/multiplayer';

    private const CLOCK_DRIFT_MAX = 60;

    /** @return array<string, mixed> */
    public static function validateOpenIdAuthToken(string $jwt, string $signingKeyDer, string $issuer, string $audience): array
    {
        try {
            if (!JwtUtils::verify($jwt, $signingKeyDer, false)) {
                throw new VerifyLoginException('Invalid JWT signature', self::translation('pocketmine_disconnect_invalidSession_badSignature'));
            }
            [, $claims] = JwtUtils::parse($jwt);
        } catch (JwtException $e) {
            throw new VerifyLoginException($e->getMessage(), null, 0, $e);
        }

        if (($claims['iss'] ?? null) !== $issuer) {
            throw new VerifyLoginException('Invalid JWT issuer');
        }
        if (($claims['aud'] ?? null) !== $audience) {
            throw new VerifyLoginException('Invalid JWT audience');
        }
        self::checkExpiry($claims);
        return $claims;
    }

    /** @return array<string, mixed> */
    public static function validateLegacyAuthToken(string $jwt, ?string $expectedKeyDer): array
    {
        self::validateSelfSignedToken($jwt, $expectedKeyDer);
        try {
            [, $claims] = JwtUtils::parse($jwt);
        } catch (JwtException $e) {
            throw new VerifyLoginException('Failed to parse JWT: ' . $e->getMessage(), null, 0, $e);
        }
        self::checkExpiry($claims);
        return $claims;
    }

    public static function validateSelfSignedToken(string $jwt, ?string $expectedKeyDer): void
    {
        try {
            [$headers] = JwtUtils::parse($jwt);
        } catch (JwtException $e) {
            throw new VerifyLoginException('Failed to parse JWT: ' . $e->getMessage(), null, 0, $e);
        }

        $x5u = $headers['x5u'] ?? null;
        if (!is_string($x5u)) {
            throw new VerifyLoginException('Invalid JWT header: missing x5u');
        }
        $headerDerKey = base64_decode($x5u, true);
        if ($headerDerKey === false) {
            throw new VerifyLoginException('Invalid JWT public key: base64 decoding error decoding x5u');
        }
        if ($expectedKeyDer !== null && $headerDerKey !== $expectedKeyDer) {
            throw new VerifyLoginException('Invalid JWT signature', self::translation('pocketmine_disconnect_invalidSession_badSignature'));
        }
        try {
            if (!JwtUtils::verify($jwt, $headerDerKey, true)) {
                throw new VerifyLoginException('Invalid JWT signature', self::translation('pocketmine_disconnect_invalidSession_badSignature'));
            }
        } catch (JwtException $e) {
            throw new VerifyLoginException($e->getMessage(), null, 0, $e);
        }
    }

    /** @param array<string, mixed> $claims */
    private static function checkExpiry(array $claims): void
    {
        $now = time();
        if (isset($claims['nbf']) && $claims['nbf'] > $now + self::CLOCK_DRIFT_MAX) {
            throw new VerifyLoginException('JWT not yet valid', self::translation('pocketmine_disconnect_invalidSession_tooEarly'));
        }
        if (isset($claims['exp']) && $claims['exp'] < $now - self::CLOCK_DRIFT_MAX) {
            throw new VerifyLoginException('JWT expired', self::translation('pocketmine_disconnect_invalidSession_tooLate'));
        }
    }

    private static function translation(string $method): mixed
    {
        return method_exists(KnownTranslationFactory::class, $method) ? KnownTranslationFactory::{$method}() : null;
    }
}
