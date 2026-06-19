<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

use pocketmine\utils\Internet;
use pocketmine\utils\InternetException;

class BulkCurlTask extends AsyncTask
{
    private const TLS_KEY_COMPLETION_CALLBACK = 'completionCallback';

    /** @var BulkCurlTaskOperation[] */
    private array $operations;

    public function __construct(array $operations, \Closure $onCompletion)
    {
        $this->operations = $operations;
        $this->storeLocal(self::TLS_KEY_COMPLETION_CALLBACK, $onCompletion);
    }

    public function onRun(): void
    {
        $results = [];
        foreach ($this->operations as $operation) {
            try {
                $results[] = Internet::simpleCurl($operation->getPage(), $operation->getTimeout(), $operation->getExtraHeaders(), $operation->getExtraOpts());
            } catch (InternetException $e) {
                $results[] = $e;
            }
        }
        $this->setResult($results);
    }

    public function onCompletion(): void
    {
        $callback = $this->fetchLocal(self::TLS_KEY_COMPLETION_CALLBACK);
        $callback($this->getResult());
    }
}
