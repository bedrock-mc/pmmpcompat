<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\inventory\Inventory;
use pocketmine\network\PacketHandlingException;
use pocketmine\network\mcpe\ComplexInventoryMapEntry;
use pocketmine\network\mcpe\ItemStackInfo;
use pocketmine\network\mcpe\JwtException;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\PacketRateLimiter;
use pocketmine\network\mcpe\auth\AuthKeyProvider;
use pocketmine\network\mcpe\auth\AuthKeyring;
use pocketmine\network\mcpe\auth\VerifyLoginException;

$encoded = JwtUtils::b64UrlEncode('pmmpcompat');
assertSame('pmmpcompat', JwtUtils::b64UrlDecode($encoded), 'base64url round trip');

$unsigned = JwtUtils::b64UrlEncode('{"alg":"none"}') . '.' . JwtUtils::b64UrlEncode('{"sub":"player"}') . '.' . JwtUtils::b64UrlEncode('');
[$header, $claims, $signature] = JwtUtils::parse($unsigned);
assertSame('none', $header['alg'] ?? null, 'JWT header parse');
assertSame('player', $claims['sub'] ?? null, 'JWT claims parse');
assertSame('', $signature, 'JWT signature parse');

try {
    JwtUtils::split('not-a-jwt');
    fail('invalid JWT should fail');
} catch (JwtException) {
}

$inventory = new Inventory(9);
$map = new ComplexInventoryMapEntry($inventory, [0 => 8, 1 => 7]);
assertSame($inventory, $map->getInventory(), 'inventory map retains inventory');
assertSame(8, $map->mapNetToCore(0), 'network slot maps to core slot');
assertSame(1, $map->mapCoreToNet(7), 'core slot maps to network slot');
assertSame(null, $map->mapNetToCore(9), 'missing network slot is null');

$stack = new ItemStackInfo(123, 456);
assertSame(123, $stack->getRequestId(), 'request ID retained');
assertSame(456, $stack->getStackId(), 'stack ID retained');

$limiter = new PacketRateLimiter('smoke', 1, 1, PHP_INT_MAX);
$limiter->decrement();
try {
    $limiter->decrement();
    fail('packet rate limiter should reject exhausted budget');
} catch (PacketHandlingException) {
}

$keyring = new AuthKeyring('issuer', ['kid' => 'der-key']);
assertSame('issuer', $keyring->getIssuer(), 'keyring issuer retained');
assertSame('der-key', $keyring->getKey('kid'), 'keyring key lookup');
assertSame(null, $keyring->getKey('missing'), 'missing key lookup');

$provider = new AuthKeyProvider(keyring: $keyring);
$resolved = null;
$provider->getKey('kid')->onCompletion(
    static function (array $value) use (&$resolved): void {
        $resolved = $value;
    },
    static function (): void {
        fail('known auth key should resolve');
    },
);
assertSame(['issuer', 'der-key'], $resolved, 'auth provider resolves known key');

$rejected = false;
$provider->getKey('missing')->onCompletion(
    static function (): void {
        fail('unknown auth key should reject');
    },
    static function () use (&$rejected): void {
        $rejected = true;
    },
);
assertSame(true, $rejected, 'auth provider rejects unknown key');

$verify = new VerifyLoginException('bad login', 'disconnect text');
assertSame('disconnect text', $verify->getDisconnectMessage(), 'disconnect message retained');

echo "mcpe core smoke ok\n";

function assertSame(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL {$label}: expected " . var_export($expected, true) . ', got ' . var_export($actual, true) . "\n");
        exit(1);
    }
}

function fail(string $message): void
{
    fwrite(STDERR, "FAIL {$message}\n");
    exit(1);
}
