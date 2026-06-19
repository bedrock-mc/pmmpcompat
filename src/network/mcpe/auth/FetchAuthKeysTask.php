<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

use pocketmine\scheduler\AsyncTask;

class FetchAuthKeysTask
{
    /** @var callable|null */
    private mixed $callback;

    /** @var array<string, mixed>|null */
    private ?array $result = null;

    public function __construct(?callable $callback = null)
    {
        $this->callback = $callback;
    }

    public function onRun(): void
    {
        $this->result = ['keys' => null, 'issuer' => '', 'errors' => ['Auth key fetching is host-managed in pmmpcompat']];
    }

    public function onCompletion(): void
    {
        if ($this->callback !== null) {
            ($this->callback)($this->result['keys'] ?? null, $this->result['issuer'] ?? '', $this->result['errors'] ?? null);
        }
    }
}
