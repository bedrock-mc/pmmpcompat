<?php

declare(strict_types=1);

namespace pocketmine\utils;

class Internet
{
    public static function getInternalIP(): string
    {
        return gethostbyname(gethostname() ?: 'localhost');
    }

    public static function getIP(bool $force = false): ?string
    {
        return self::getURL('https://api.ipify.org', 10);
    }

    public static function getURL(string $page, int|float $timeout = 10, array $extraHeaders = [], &$err = null): ?string
    {
        $headers = '';
        foreach ($extraHeaders as $name => $value) {
            $headers .= is_int($name) ? (string) $value . "\r\n" : $name . ': ' . $value . "\r\n";
        }
        $context = stream_context_create(['http' => ['timeout' => $timeout, 'header' => $headers]]);
        $result = @file_get_contents($page, false, $context);
        if ($result === false) {
            $err = error_get_last()['message'] ?? 'request failed';
            return null;
        }
        return $result;
    }

    public static function postURL(string $page, array|string $args, int|float $timeout = 10, array $extraHeaders = [], &$err = null): ?string
    {
        $body = is_array($args) ? http_build_query($args) : $args;
        $headers = "Content-Type: application/x-www-form-urlencoded\r\n";
        foreach ($extraHeaders as $name => $value) {
            $headers .= is_int($name) ? (string) $value . "\r\n" : $name . ': ' . $value . "\r\n";
        }
        $context = stream_context_create(['http' => ['method' => 'POST', 'timeout' => $timeout, 'header' => $headers, 'content' => $body]]);
        $result = @file_get_contents($page, false, $context);
        if ($result === false) {
            $err = error_get_last()['message'] ?? 'request failed';
            return null;
        }
        return $result;
    }

    /** @param array<string, string>|list<string> $extraHeaders */
    public static function simpleCurl(string $page, float $timeout = 10, array $extraHeaders = [], array $extraOpts = [], ?\Closure $onSuccess = null): InternetRequestResult
    {
        $err = null;
        $body = self::getURL($page, $timeout, $extraHeaders, $err);
        if ($body === null) {
            throw new InternetException($err ?? 'request failed');
        }
        $result = new InternetRequestResult([], $body, 200);
        if ($onSuccess !== null) {
            $onSuccess($result);
        }
        return $result;
    }
}
