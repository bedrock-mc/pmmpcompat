<?php

declare(strict_types=1);

namespace pocketmine\scheduler;

class TimingsControlTask extends AsyncTask
{
    private function __construct(private ?bool $enabled)
    {
    }

    public static function setEnabled(bool $enable): self
    {
        return new self($enable);
    }

    public static function reload(): self
    {
        return new self(null);
    }

    public function onRun(): void
    {
        $this->setResult($this->enabled);
    }
}
