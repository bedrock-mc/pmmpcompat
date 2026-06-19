<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$repoRoot = dirname($root, 2);
$reference = $argv[1] ?? $repoRoot . '/refs/pocketmine/PocketMine-MP/src';
$facade = $argv[2] ?? $root . '/src';

if (!is_dir($reference)) {
    fwrite(STDERR, "reference path not found: {$reference}\n");
    exit(2);
}
if (!is_dir($facade)) {
    fwrite(STDERR, "facade path not found: {$facade}\n");
    exit(2);
}

$referenceApi = scanApi($reference);
$facadeApi = scanApi($facade);

$missingClasses = [];
$missingMembers = [];
foreach ($referenceApi as $class => $shape) {
    if (!str_starts_with($class, 'pocketmine\\')) {
        continue;
    }
    if (!isset($facadeApi[$class])) {
        $missingClasses[$class] = $shape;
        continue;
    }
    foreach (['methods', 'constants'] as $kind) {
        foreach (array_keys($shape[$kind]) as $name) {
            if (!isset($facadeApi[$class][$kind][$name])) {
                $missingMembers[$class][$kind][] = $name;
            }
        }
    }
}

ksort($missingClasses);
ksort($missingMembers);

$summary = [
    'reference_classes' => count($referenceApi),
    'facade_classes' => count($facadeApi),
    'missing_classes' => count($missingClasses),
    'classes_with_missing_members' => count($missingMembers),
];

$report = [
    'summary' => $summary,
    'missing_classes_sample' => array_slice(array_keys($missingClasses), 0, 100),
    'missing_members_sample' => array_slice($missingMembers, 0, 100, true),
];

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

/** @return array<string, array{type: string, methods: array<string, true>, constants: array<string, true>}> */
function scanApi(string $dir): array
{
    $api = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        foreach (scanFile($file->getPathname()) as $class => $shape) {
            $api[$class] = $shape;
        }
    }
    ksort($api);
    return $api;
}

/** @return array<string, array{type: string, methods: array<string, true>, constants: array<string, true>}> */
function scanFile(string $path): array
{
    $tokens = token_get_all((string) file_get_contents($path));
    $namespace = '';
    $classes = [];
    $count = count($tokens);
    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];
        if (!is_array($token)) {
            continue;
        }
        if ($token[0] === T_NAMESPACE) {
            $namespace = readNamespace($tokens, $i + 1);
            continue;
        }
        if (!in_array($token[0], [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM], true)) {
            continue;
        }
        if (previousMeaningfulToken($tokens, $i) === T_NEW) {
            continue;
        }
        $type = token_name($token[0]);
        $name = readNextIdentifier($tokens, $i + 1);
        if ($name === null) {
            continue;
        }
        $fqcn = ltrim($namespace . '\\' . $name, '\\');
        $bodyStart = findNextChar($tokens, $i, '{');
        if ($bodyStart === null) {
            continue;
        }
        $bodyEnd = findMatchingBrace($tokens, $bodyStart);
        if ($bodyEnd === null) {
            continue;
        }
        $classes[$fqcn] = [
            'type' => $type,
            'methods' => scanClassMethods($tokens, $bodyStart + 1, $bodyEnd),
            'constants' => scanClassConstants($tokens, $bodyStart + 1, $bodyEnd),
        ];
        $i = $bodyEnd;
    }
    return $classes;
}

/** @param array<int, mixed> $tokens */
function readNamespace(array $tokens, int $start): string
{
    $parts = [];
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        $token = $tokens[$i];
        if ($token === ';' || $token === '{') {
            break;
        }
        if (is_array($token) && in_array($token[0], [T_STRING, T_NAME_QUALIFIED, T_NS_SEPARATOR], true)) {
            $parts[] = $token[1];
        }
    }
    return implode('', $parts);
}

/** @param array<int, mixed> $tokens */
function readNextIdentifier(array $tokens, int $start): ?string
{
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        $token = $tokens[$i];
        if (is_array($token) && $token[0] === T_STRING) {
            return $token[1];
        }
        if ($token === '{' || $token === ';') {
            return null;
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function previousMeaningfulToken(array $tokens, int $offset): ?int
{
    for ($i = $offset - 1; $i >= 0; $i--) {
        $token = $tokens[$i];
        if (is_array($token) && in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            continue;
        }
        return is_array($token) ? $token[0] : null;
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function findNextChar(array $tokens, int $start, string $char): ?int
{
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        if ($tokens[$i] === $char) {
            return $i;
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens */
function findMatchingBrace(array $tokens, int $start): ?int
{
    $depth = 0;
    for ($i = $start, $count = count($tokens); $i < $count; $i++) {
        if ($tokens[$i] === '{') {
            $depth++;
        } elseif ($tokens[$i] === '}') {
            $depth--;
            if ($depth === 0) {
                return $i;
            }
        }
    }
    return null;
}

/** @param array<int, mixed> $tokens @return array<string, true> */
function scanClassMethods(array $tokens, int $start, int $end): array
{
    $methods = [];
    $depth = 0;
    $public = false;
    for ($i = $start; $i < $end; $i++) {
        $token = $tokens[$i];
        if ($token === '{') {
            $depth++;
            continue;
        }
        if ($token === '}') {
            $depth--;
            continue;
        }
        if ($depth !== 0 || !is_array($token)) {
            continue;
        }
        if ($token[0] === T_PUBLIC) {
            $public = true;
            continue;
        }
        if (in_array($token[0], [T_PRIVATE, T_PROTECTED], true)) {
            $public = false;
            continue;
        }
        if ($token[0] === T_FUNCTION) {
            $name = readNextIdentifier($tokens, $i + 1);
            if ($name !== null && $public) {
                $methods[$name] = true;
            }
            $public = false;
        }
    }
    ksort($methods);
    return $methods;
}

/** @param array<int, mixed> $tokens @return array<string, true> */
function scanClassConstants(array $tokens, int $start, int $end): array
{
    $constants = [];
    $depth = 0;
    $public = false;
    for ($i = $start; $i < $end; $i++) {
        $token = $tokens[$i];
        if ($token === '{') {
            $depth++;
            continue;
        }
        if ($token === '}') {
            $depth--;
            continue;
        }
        if ($depth !== 0 || !is_array($token)) {
            continue;
        }
        if ($token[0] === T_PUBLIC) {
            $public = true;
            continue;
        }
        if (in_array($token[0], [T_PRIVATE, T_PROTECTED], true)) {
            $public = false;
            continue;
        }
        if ($token[0] === T_CONST && $public) {
            $name = readNextIdentifier($tokens, $i + 1);
            if ($name !== null) {
                $constants[$name] = true;
            }
            $public = false;
        }
    }
    ksort($constants);
    return $constants;
}
