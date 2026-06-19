<?php

declare(strict_types=1);

namespace pocketmine\network\query;

use pocketmine\network\AdvancedNetworkInterface;
use pocketmine\network\RawPacketHandler;
use function hash;
use function method_exists;
use function pack;
use function random_bytes;
use function strlen;
use function substr;
use function unpack;

class QueryHandler implements RawPacketHandler
{
    public const HANDSHAKE = 9;
    public const STATISTICS = 0;

    private string $lastToken;
    private string $token;

    public function __construct(private mixed $server = null)
    {
        $this->token = $this->generateToken();
        $this->lastToken = $this->token;
    }

    public function getPattern(): string
    {
        return '/^\xfe\xfd.+$/s';
    }

    private function generateToken(): string
    {
        return random_bytes(16);
    }

    public function regenerateToken(): void
    {
        $this->lastToken = $this->token;
        $this->token = $this->generateToken();
    }

    public static function getTokenString(string $token, string $salt): int
    {
        $unsigned = unpack('N', substr(hash('sha512', $salt . ':' . $token, true), 7, 4))[1];
        return $unsigned >= 0x80000000 ? $unsigned - 0x100000000 : $unsigned;
    }

    public function handle(AdvancedNetworkInterface $interface, string $address, int $port, string $packet): bool
    {
        if (strlen($packet) < 7 || substr($packet, 0, 2) !== "\xfe\xfd") {
            return false;
        }
        $packetType = ord($packet[2]);
        $sessionID = unpack('N', substr($packet, 3, 4))[1];

        if ($packetType === self::HANDSHAKE) {
            $payload = chr(self::HANDSHAKE) . pack('N', $sessionID) . self::getTokenString($this->token, $address) . "\x00";
            $interface->sendRawPacket($address, $port, $payload);
            return true;
        }

        if ($packetType === self::STATISTICS) {
            if (strlen($packet) < 11) {
                return false;
            }
            $token = $this->unpackSignedInt(substr($packet, 7, 4));
            if ($token !== self::getTokenString($this->token, $address) && $token !== self::getTokenString($this->lastToken, $address)) {
                return true;
            }
            $query = $this->getQueryInfo();
            $payload = chr(self::STATISTICS) . pack('N', $sessionID);
            $payload .= strlen($packet) - 11 === 4 ? $query->getLongQuery() : $query->getShortQuery();
            $interface->sendRawPacket($address, $port, $payload);
            return true;
        }

        return false;
    }

    private function getQueryInfo(): QueryInfo
    {
        if (is_object($this->server) && method_exists($this->server, 'getQueryInformation')) {
            $info = $this->server->getQueryInformation();
            if ($info instanceof QueryInfo) {
                return $info;
            }
        }
        return new QueryInfo($this->server);
    }

    private function unpackSignedInt(string $bytes): int
    {
        $unsigned = unpack('N', $bytes)[1];
        return $unsigned >= 0x80000000 ? $unsigned - 0x100000000 : $unsigned;
    }
}
