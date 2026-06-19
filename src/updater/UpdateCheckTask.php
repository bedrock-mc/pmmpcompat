<?php

declare(strict_types=1);

namespace pocketmine\updater;

use pocketmine\scheduler\AsyncTask;

class UpdateCheckTask extends AsyncTask
{
    private const TLS_KEY_UPDATER = 'updater';
    private string $error = 'Network update checks are disabled by pmmpcompat';

    public function __construct(UpdateChecker $updater, private string $endpoint, private string $channel)
    {
        $this->storeLocal(self::TLS_KEY_UPDATER, $updater);
    }

    public function onRun(): void {}

    public function onCompletion(): void
    {
        $this->fetchLocal(self::TLS_KEY_UPDATER)->checkUpdateError($this->error);
    }
}
