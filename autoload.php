<?php

declare(strict_types=1);

defined('PMMPCOMPAT_AUTOLOADER_PATH') || define('PMMPCOMPAT_AUTOLOADER_PATH', __FILE__);

$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(static function (string $class): void {
    $overrides = [
        'cooldogedev\\BedrockEconomy\\libs\\cooldogedev\\libSQL\\context\\ClosureContext' => __DIR__ . '/src/compat/overrides/BedrockEconomyClosureContext.php',
    ];
    if (isset($overrides[$class])) {
        require $overrides[$class];
        return;
    }

    $prefixes = [
        'pocketmine\\' => __DIR__ . '/src/',
        'pmmp\\thread\\' => __DIR__ . '/src/pmmp/thread/',
    ];
    foreach ($prefixes as $prefix => $root) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }
        $file = $root . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require $file;
        }
        return;
    }
});

if (!class_exists('Threaded', false)) {
    class Threaded extends \pmmp\thread\ThreadSafe implements \ArrayAccess, \Countable, \IteratorAggregate
    {
        public function shift(): mixed
        {
            $keys = array_keys($this->values());
            if ($keys === []) {
                return null;
            }
            $key = $keys[0];
            $value = $this->{$key};
            unset($this->{$key});
            return $value;
        }

        public function count(): int
        {
            return count($this->values());
        }

        public function getIterator(): \Iterator
        {
            return new \ArrayIterator($this->values());
        }

        public function offsetExists(mixed $offset): bool
        {
            return isset($this->{(string) $offset});
        }

        public function offsetGet(mixed $offset): mixed
        {
            return $this->{(string) $offset} ?? null;
        }

        public function offsetSet(mixed $offset, mixed $value): void
        {
            if ($offset === null) {
                $numeric = array_filter(array_keys($this->values()), 'is_int');
                $offset = $numeric === [] ? 0 : max($numeric) + 1;
            }
            $this->{(string) $offset} = $value;
        }

        public function offsetUnset(mixed $offset): void
        {
            unset($this->{(string) $offset});
        }

        /** @return array<int|string, mixed> */
        private function values(): array
        {
            $values = get_object_vars($this);
            foreach (array_keys($values) as $key) {
                if (is_numeric($key)) {
                    $values[(int) $key] = $values[$key];
                    unset($values[$key]);
                }
            }
            return $values;
        }
    }
}

if (!class_exists('Volatile', false)) {
    class Volatile extends Threaded {}
}

defined('PTHREADS_INHERIT_NONE') || define('PTHREADS_INHERIT_NONE', \pmmp\thread\Thread::INHERIT_ALL);
defined('PTHREADS_INHERIT_ALL') || define('PTHREADS_INHERIT_ALL', \pmmp\thread\Thread::INHERIT_ALL);
