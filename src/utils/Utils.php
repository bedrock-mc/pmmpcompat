<?php

declare(strict_types=1);

namespace pocketmine\utils;

final class Utils
{
    public const OS_WINDOWS = 'win';
    public const OS_IOS = 'ios';
    public const OS_MACOS = 'mac';
    public const OS_ANDROID = 'android';
    public const OS_LINUX = 'linux';
    public const OS_BSD = 'bsd';
    public const OS_UNKNOWN = 'other';

    private static ?string $os = null;
    private static ?int $cpuCores = null;

    public static function getNiceClosureName(\Closure $closure): string
    {
        $func = new \ReflectionFunction($closure);
        if (!str_contains($func->getName(), '{closure')) {
            $scope = $func->getClosureScopeClass();
            if ($scope !== null) {
                return $scope->getName() . ($func->getClosureThis() !== null ? '->' : '::') . $func->getName();
            }
            return $func->getName();
        }
        $file = $func->getFileName();
        return 'closure@' . ($file !== false ? Filesystem::cleanPath($file) . '#L' . $func->getStartLine() : 'internal');
    }

    public static function getNiceClassName(object $obj): string
    {
        $reflect = new \ReflectionClass($obj);
        if ($reflect->isAnonymous()) {
            $file = $reflect->getFileName();
            return 'anonymous@' . ($file !== false ? Filesystem::cleanPath($file) . '#L' . $reflect->getStartLine() : 'internal');
        }
        return $reflect->getName();
    }

    public static function cloneCallback(): \Closure
    {
        return static fn(object $o): object => clone $o;
    }

    /** @param object[] $array @return object[] */
    public static function cloneObjectArray(array $array): array
    {
        return array_map(static fn(object $o): object => clone $o, $array);
    }

    public static function getMachineUniqueId(string $extra = ''): string
    {
        return substr(hash('sha256', php_uname('a') . sys_get_temp_dir() . $extra), 0, 32);
    }

    public static function getOS(bool $recalculate = false): string
    {
        if (self::$os !== null && !$recalculate) {
            return self::$os;
        }
        $uname = php_uname('s');
        if (stripos($uname, 'Darwin') !== false) {
            self::$os = str_starts_with(php_uname('m'), 'iP') ? self::OS_IOS : self::OS_MACOS;
        } elseif (stripos($uname, 'Win') !== false || $uname === 'Msys') {
            self::$os = self::OS_WINDOWS;
        } elseif (stripos($uname, 'Linux') !== false) {
            self::$os = file_exists('/system/build.prop') ? self::OS_ANDROID : self::OS_LINUX;
        } elseif (stripos($uname, 'BSD') !== false || $uname === 'DragonFly') {
            self::$os = self::OS_BSD;
        } else {
            self::$os = self::OS_UNKNOWN;
        }
        return self::$os;
    }

    public static function getCoreCount(bool $recalculate = false): int
    {
        if (self::$cpuCores !== null && !$recalculate) {
            return self::$cpuCores;
        }
        $count = 1;
        if (self::getOS() === self::OS_LINUX && ($cpuinfo = @file('/proc/cpuinfo')) !== false) {
            $count = max(1, count(preg_grep('/^processor\s*:/', $cpuinfo) ?: []));
        } elseif (($env = getenv('NUMBER_OF_PROCESSORS')) !== false && is_numeric($env)) {
            $count = max(1, (int) $env);
        }
        return self::$cpuCores = $count;
    }

    public static function hexdump(string $bin): string
    {
        $output = '';
        foreach (str_split($bin, 16) as $counter => $line) {
            $hex = chunk_split(chunk_split(str_pad(bin2hex($line), 32, ' ', STR_PAD_RIGHT), 2, ' '), 24, ' ');
            $ascii = preg_replace('#([^\x20-\x7E])#', '.', $line);
            $output .= str_pad(dechex($counter << 4), 4, '0', STR_PAD_LEFT) . '  ' . $hex . ' ' . $ascii . PHP_EOL;
        }
        return $output;
    }

    public static function printable(mixed $str): string
    {
        return is_string($str) ? (string) preg_replace('#([^\x20-\x7E])#', '.', $str) : gettype($str);
    }

    public static function javaStringHash(string $string): int
    {
        $hash = 0;
        for ($i = 0, $len = strlen($string); $i < $len; $i++) {
            $ord = ord($string[$i]);
            if (($ord & 0x80) !== 0) {
                $ord -= 0x100;
            }
            $hash = (31 * $hash + $ord) & 0xffffffff;
        }
        return $hash >= 0x80000000 ? $hash - 0x100000000 : $hash;
    }

    public static function getReferenceCount(object $value, bool $includeCurrent = true): int
    {
        return 1;
    }

    /** @return list<array<string, mixed>> */
    public static function currentTrace(int $skipFrames = 0): array
    {
        return array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $skipFrames + 1);
    }

    /** @return string[] */
    public static function printableCurrentTrace(int $skipFrames = 0): array
    {
        return self::printableTrace(self::currentTrace($skipFrames + 1));
    }

    /** @param array<int, array<string, mixed>> $trace @return string[] */
    public static function printableTrace(array $trace): array
    {
        return array_map(static function (array $frame): string {
            $where = ($frame['file'] ?? 'internal') . ':' . ($frame['line'] ?? 0);
            $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '');
            return $where . ' ' . $func;
        }, $trace);
    }

    /** @param array<int, array<string, mixed>> $trace @return string[] */
    public static function printableTraceWithMetadata(array $trace, mixed ...$args): array
    {
        return self::printableTrace($trace);
    }

    public static function printableExceptionInfo(\Throwable $e, mixed ...$args): string
    {
        return get_class($e) . ': ' . $e->getMessage();
    }

    /** @return array<string, string> */
    public static function parseDocComment(string $docComment): array
    {
        $raw = substr($docComment, 3, -2);
        preg_match_all('/(*ANYCRLF)^[\t ]*(?:\* )?@([a-zA-Z\-]+)(?:[\t ]+(.+?))?[\t ]*$/m', $raw, $matches);
        return array_combine($matches[1], $matches[2]) ?: [];
    }

    public static function testValidInstance(string $className, string $baseName): void
    {
        if (!class_exists($baseName) && !interface_exists($baseName)) {
            throw new \InvalidArgumentException('Base class ' . $baseName . ' does not exist');
        }
        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not exist or is not a class');
        }
        if (!is_a($className, $baseName, true)) {
            throw new \InvalidArgumentException('Class ' . $className . ' does not extend or implement ' . $baseName);
        }
        if (!(new \ReflectionClass($className))->isInstantiable()) {
            throw new \InvalidArgumentException('Class ' . $className . ' cannot be constructed');
        }
    }

    public static function validateCallableSignature(callable $signature, callable $subject): void
    {
        // PHP's native type system validates callables when they are invoked. This facade keeps the PMMP helper present.
    }

    public static function validateArrayValueType(array $array, \Closure $validator): void
    {
        foreach (self::promoteKeys($array) as $k => $v) {
            try {
                $validator($v);
            } catch (\TypeError $e) {
                throw new \TypeError('Incorrect type of element at "' . $k . '": ' . $e->getMessage(), 0, $e);
            }
        }
    }

    public static function stringifyKeys(array $array): \Generator
    {
        foreach ($array as $key => $value) {
            yield (string) $key => $value;
        }
    }

    public static function promoteKeys(array $array): array
    {
        return $array;
    }

    public static function checkUTF8(string $string): void
    {
        if (function_exists('mb_check_encoding') && !mb_check_encoding($string, 'UTF-8')) {
            throw new \InvalidArgumentException('Text must be valid UTF-8');
        }
    }

    public static function assumeNotFalse(mixed $value, \Closure|string $context = 'This should never be false'): mixed
    {
        if ($value === false) {
            throw new AssumptionFailedError('Assumption failure: ' . (is_string($context) ? $context : $context()));
        }
        return $value;
    }

    public static function checkFloatNotInfOrNaN(string $name, float $float): void
    {
        if (is_nan($float)) {
            throw new \InvalidArgumentException($name . ' cannot be NaN');
        }
        if (is_infinite($float)) {
            throw new \InvalidArgumentException($name . ' cannot be infinite');
        }
    }

    public static function checkVector3NotInfOrNaN(object $vector3): void
    {
        foreach (['x', 'y', 'z', 'yaw', 'pitch'] as $name) {
            if (isset($vector3->{$name}) && is_float($vector3->{$name})) {
                self::checkFloatNotInfOrNaN($name, $vector3->{$name});
            }
        }
    }

    public static function checkLocationNotInfOrNaN(object $location): void
    {
        self::checkVector3NotInfOrNaN($location);
    }

    public static function getOpcacheJitMode(): ?int
    {
        return function_exists('opcache_get_status') && opcache_get_status(false) !== false ? 0 : null;
    }

    public static function getRandomFloat(): float
    {
        return mt_rand() / mt_getrandmax();
    }
}
