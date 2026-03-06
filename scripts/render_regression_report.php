<?php

declare(strict_types=1);

$options = getopt('', ['input:', 'output:']);

$inputPath = isset($options['input']) ? (string) $options['input'] : '/app/reports/regression/result-local-php.json';
$outputPath = isset($options['output']) ? (string) $options['output'] : '/app/reports/regression/report.md';

if (!is_file($inputPath)) {
    fwrite(STDERR, "Regression input file not found: {$inputPath}\n");
    exit(2);
}

$raw = file_get_contents($inputPath);
$decoded = is_string($raw) ? json_decode($raw, true) : null;

if (!is_array($decoded) || !isset($decoded['checks']) || !is_array($decoded['checks'])) {
    fwrite(STDERR, "Invalid regression result JSON.\n");
    exit(3);
}

$summary = isset($decoded['summary']) && is_array($decoded['summary']) ? $decoded['summary'] : [];
$passed = isset($summary['passed']) ? (int) $summary['passed'] : 0;
$total = isset($summary['total']) ? (int) $summary['total'] : count($decoded['checks']);

$lines = [];
$lines[] = '# Regression workflow report';
$lines[] = '';
$lines[] = '- Generated: '.gmdate('Y-m-d H:i:s').' UTC';
$lines[] = '- Base URL: '.(isset($decoded['base_url']) ? (string) $decoded['base_url'] : 'n/a');
$lines[] = '- Result: '.$passed.'/'.$total;
$lines[] = '';
$lines[] = '| Check | Method | Path | Status | Result |';
$lines[] = '|---|---|---|---:|---|';

foreach ($decoded['checks'] as $check) {
    $name = isset($check['name']) ? (string) $check['name'] : 'unknown';
    $method = isset($check['method']) ? (string) $check['method'] : 'GET';
    $path = isset($check['path']) ? (string) $check['path'] : '/';
    $status = isset($check['status']) ? (int) $check['status'] : 0;
    $result = isset($check['pass']) && $check['pass'] === true ? 'PASS' : 'FAIL';

    $lines[] = '| '.$name.' | '.$method.' | '.$path.' | '.$status.' | '.$result.' |';
}

$lines[] = '';
$lines[] = '## Failures';
$lines[] = '';

$failureCount = 0;
foreach ($decoded['checks'] as $check) {
    if (!isset($check['pass']) || $check['pass'] !== true) {
        $failureCount++;
        $name = isset($check['name']) ? (string) $check['name'] : 'unknown';
        $path = isset($check['path']) ? (string) $check['path'] : '/';
        $status = isset($check['status']) ? (int) $check['status'] : 0;
        $snippet = isset($check['snippet']) ? trim((string) $check['snippet']) : '';

        $lines[] = '- '.$name.' ('.$path.', HTTP '.$status.')';
        if ($snippet !== '') {
            $lines[] = '  - Snippet: '.$snippet;
        }
    }
}

if ($failureCount === 0) {
    $lines[] = '- None';
}

$dir = dirname($outputPath);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

file_put_contents($outputPath, implode("\n", $lines)."\n");

echo $outputPath, "\n";
