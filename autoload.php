<?php

declare(strict_types=1);

defined('PMMPCOMPAT_AUTOLOADER_PATH') || define('PMMPCOMPAT_AUTOLOADER_PATH', __FILE__);

spl_autoload_register(static function (string $class): void {
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
