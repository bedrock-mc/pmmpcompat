<?php
declare(strict_types=1);

$root = dirname(__DIR__);

$phpActions = collectPhpActions($root . '/src/compat');
$goActions = collectGoActions($root . '/host/go/actions.go');

$missingInGo = array_values(array_diff($phpActions, $goActions));
$staleInGo = array_values(array_diff($goActions, $phpActions));

$result = [
    'php_emitted_actions' => $phpActions,
    'go_dispatch_actions' => $goActions,
    'missing_in_go' => $missingInGo,
    'stale_in_go' => $staleInGo,
    'summary' => [
        'php_emitted' => count($phpActions),
        'go_dispatch' => count($goActions),
        'missing_in_go' => count($missingInGo),
        'stale_in_go' => count($staleInGo),
    ],
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

if ($missingInGo !== [] || $staleInGo !== []) {
    exit(1);
}

/** @return list<string> */
function collectPhpActions(string $dir): array
{
    $actions = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }
        $source = (string) file_get_contents($file->getPathname());
        if (preg_match_all("/['\"]type['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $source, $matches)) {
            foreach ($matches[1] as $action) {
                $actions[$action] = true;
            }
        }
    }
    $list = array_keys($actions);
    sort($list);
    return $list;
}

/** @return list<string> */
function collectGoActions(string $file): array
{
    $source = (string) file_get_contents($file);
    preg_match_all('/case\s+"([^"]+)":/', $source, $matches);
    $actions = array_values(array_unique($matches[1]));
    sort($actions);
    return $actions;
}
