<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class TimingsCollectionTask extends AsyncTask
{
    public function __construct(private mixed $promiseResolver = null)
    {
    }

    public function onRun(): void
    {
        $this->setResult([]);
    }

    public function onCompletion(): void
    {
        if (is_object($this->promiseResolver) && method_exists($this->promiseResolver, 'resolve')) {
            $this->promiseResolver->resolve($this->getResult());
        }
    }
}
