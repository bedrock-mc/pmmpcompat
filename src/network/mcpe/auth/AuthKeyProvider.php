<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\auth;

use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use function time;

class AuthKeyProvider
{
    private const ALLOWED_REFRESH_INTERVAL = 1800;

    private ?AuthKeyring $keyring;
    private int $lastFetch;

    public function __construct(
        private mixed $logger = null,
        private mixed $asyncPool = null,
        private int $keyRefreshIntervalSeconds = self::ALLOWED_REFRESH_INTERVAL,
        ?AuthKeyring $keyring = null,
    ) {
        $this->keyring = $keyring;
        $this->lastFetch = $keyring === null ? 0 : time();
    }

    /** @return Promise resolves to array{string, string} */
    public function getKey(string $keyId): Promise
    {
        $resolver = new PromiseResolver();
        if ($this->keyring !== null && ($key = $this->keyring->getKey($keyId)) !== null) {
            $resolver->resolve([$this->keyring->getIssuer(), $key]);
            return $resolver->getPromise();
        }

        $this->log('debug', "Key $keyId not recognised");
        $resolver->reject();
        return $resolver->getPromise();
    }

    public function setKeyring(AuthKeyring $keyring): void
    {
        $this->keyring = $keyring;
        $this->lastFetch = time();
    }

    public function getKeyRefreshIntervalSeconds(): int
    {
        return $this->keyRefreshIntervalSeconds;
    }

    private function log(string $level, string $message): void
    {
        if (is_object($this->logger) && method_exists($this->logger, $level)) {
            $this->logger->{$level}($message);
        }
    }
}
