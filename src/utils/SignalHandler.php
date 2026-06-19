<?php

declare(strict_types=1);

namespace pocketmine\utils;

class SignalHandler
{
    private ?\Closure $interruptCallback = null;

    public function __construct(\Closure $interruptCallback)
    {
        $this->interruptCallback = $interruptCallback;
        if (function_exists('pcntl_signal')) {
            foreach ([SIGTERM, SIGINT, SIGHUP] as $signal) {
                pcntl_signal($signal, fn(): mixed => $interruptCallback());
            }
            if (function_exists('pcntl_async_signals')) {
                pcntl_async_signals(true);
            }
        }
    }

    public function unregister(): void
    {
        if (function_exists('pcntl_signal')) {
            foreach ([SIGTERM, SIGINT, SIGHUP] as $signal) {
                pcntl_signal($signal, SIG_DFL);
            }
        }
    }
}
