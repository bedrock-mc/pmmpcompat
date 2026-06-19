<?php

declare(strict_types=1);

require dirname(__DIR__) . '/autoload.php';

use pocketmine\network\mcpe\handler\HandshakePacketHandler;
use pocketmine\network\mcpe\handler\InGamePacketHandler;
use pocketmine\network\mcpe\handler\ItemStackContainerIdTranslator;
use pocketmine\network\mcpe\handler\ItemStackRequestExecutor;
use pocketmine\network\mcpe\handler\PacketHandler;
use pocketmine\network\mcpe\handler\ResourcePacksPacketHandler;
use pocketmine\network\mcpe\handler\SpawnResponsePacketHandler;

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$base = new class extends PacketHandler {
    public function handleExample(mixed $packet): bool
    {
        return $packet === 'ok';
    }
};
assert_true($base->handlePacket(new class {
}) === false, 'unknown packet should be rejected');
assert_true($base->handleExample('ok') === true, 'direct handler method should work');

$handshakeCompleted = false;
$handshake = new HandshakePacketHandler(static function () use (&$handshakeCompleted): void {
    $handshakeCompleted = true;
});
assert_true($handshake->handleClientToServerHandshake() === true, 'handshake packet should be accepted');
assert_true($handshakeCompleted && $handshake->isCompleted(), 'handshake callback should run once');

[$windowId, $slotId] = ItemStackContainerIdTranslator::translate(37, 9, 44);
assert_true($windowId === ItemStackContainerIdTranslator::CONTAINER_OFFHAND && $slotId === 0, 'offhand slot should be normalized');
[$windowId, $slotId] = ItemStackContainerIdTranslator::translate(8, 9, 3);
assert_true($windowId === 9 && $slotId === 3, 'open-window slot should use current window ID');

$request = [
    'requestId' => 77,
    'actions' => [
        [
            'source' => ['containerId' => 6, 'slotId' => 1],
            'destination' => ['containerId' => 8, 'slotId' => 3],
        ],
    ],
];
$executor = new ItemStackRequestExecutor(null, null, $request);
$response = $executor->buildItemStackResponse();
assert_true($response['requestId'] === 77, 'response should keep request ID');
assert_true(count($response['containers']) === 2, 'response should include changed source and destination containers');

$ingame = new InGamePacketHandler(null, null, null);
$responses = $ingame->handleItemStackRequest(['requests' => [$request]]);
assert_true($responses[0]['requestId'] === 77, 'in-game handler should build item-stack responses');
assert_true($ingame->handleMovePlayer(['x' => 1]) === true, 'dynamic gameplay handlers should record events');

$completed = false;
$resourcePacks = new ResourcePacksPacketHandler(completionCallback: static function () use (&$completed): void {
    $completed = true;
});
$resourcePacks->setUp();
assert_true($resourcePacks->getStatus() === 'info-sent', 'resource pack setup should mark info sent');
$resourcePacks->handleResourcePackClientResponse(['status' => 'completed']);
assert_true($completed && $resourcePacks->getStatus() === 'complete', 'resource pack completion callback should run');

$initialized = false;
$spawn = new SpawnResponsePacketHandler(onInitialized: static function () use (&$initialized): void {
    $initialized = true;
});
$spawn->handleSetLocalPlayerAsInitialized();
assert_true($initialized && $spawn->isInitialized(), 'spawn initialization callback should run');

echo "mcpe handler smoke ok\n";
