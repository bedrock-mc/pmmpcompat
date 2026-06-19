<?php

declare(strict_types=1);

namespace pocketmine\event\server;

use pocketmine\network\query\QueryInfo;

class QueryRegenerateEvent extends ServerEvent
{
    public function __construct(private QueryInfo $queryInfo) {}

    public function getQueryInfo(): QueryInfo { return $this->queryInfo; }
}
