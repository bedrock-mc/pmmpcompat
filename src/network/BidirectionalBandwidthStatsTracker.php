<?php

declare(strict_types=1);

namespace pocketmine\network;

final class BidirectionalBandwidthStatsTracker
{
    private BandwidthStatsTracker $send;
    private BandwidthStatsTracker $receive;

    public function __construct(int $historySize)
    {
        $this->send = new BandwidthStatsTracker($historySize);
        $this->receive = new BandwidthStatsTracker($historySize);
    }

    public function getSend(): BandwidthStatsTracker
    {
        return $this->send;
    }

    public function getReceive(): BandwidthStatsTracker
    {
        return $this->receive;
    }

    public function add(int $sendBytes, int $recvBytes): void
    {
        $this->send->add($sendBytes);
        $this->receive->add($recvBytes);
    }

    public function rotateAverageHistory(): void
    {
        $this->send->rotateHistory();
        $this->receive->rotateHistory();
    }

    public function resetHistory(): void
    {
        $this->send->resetHistory();
        $this->receive->resetHistory();
    }
}
