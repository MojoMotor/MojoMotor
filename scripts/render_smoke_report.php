<?php

declare(strict_types=1);

$options = getopt('', ['input-dir:', 'output:']);

$inputDir = isset($options['input-dir']) ? (string) $options['input-dir'] : '/app/reports/smoke';
$outputPath = isset($options['output']) ? (string) $options['output'] : '/app/reports/smoke/report.md';

$resultFiles = glob(rtrim($inputDir, '/').'/result-*.json');
$labels = [];

if (is_array($resultFiles)) {
    foreach ($resultFiles as $file) {
        $base = basename($file);
        $labels[] = substr($base, 7, -5);
    }
}

if (count($labels) === 0) {
    $labels = ['8.1', '8.2', '8.3'];
}

usort($labels, static function (string $a, string $b): int {
    $order = ['8.1' => 1, '8.2' => 2, '8.3' => 3];
    $oa = $order[$a] ?? 99;
    $ob = $order[$b] ?? 99;

    if ($oa === $ob) {
        return strcmp($a, $b);
    }

    return $oa <=> $ob;
});
$dateUtc = gmdate('Y-m-d H:i:s').' UTC';

$lines = [];
$lines[] = '# PHP smoke-check report';
$lines[] = '';
$lines[] = '- Generated: '.$dateUtc;
$lines[] = '- Endpoints: `/`, `/setup`, `/index.php/admin/login`, `/index.php/page/content`';
$lines[] = '';
$lines[] = '| PHP | Pass/Total | First fatal |';
$lines[] = '|---|---:|---|';

foreach ($labels as $label) {
    $resultFile = rtrim($inputDir, '/').'/result-'.$label.'.json';

    if (!is_file($resultFile)) {
        $lines[] = '| '.$label.' | n/a | Result file missing |';
        continue;
    }

    $raw = file_get_contents($resultFile);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;

    if (!is_array($decoded) || !isset($decoded['summary'])) {
        $lines[] = '| '.$label.' | n/a | Invalid result JSON |';
        continue;
    }

    $summary = $decoded['summary'];
    $passed = isset($summary['passed']) ? (int) $summary['passed'] : 0;
    $total = isset($summary['total']) ? (int) $summary['total'] : 0;

    $fatalText = 'None';
    if (isset($summary['first_fatal']) && is_array($summary['first_fatal'])) {
        $fatalPath = isset($summary['first_fatal']['path']) ? (string) $summary['first_fatal']['path'] : 'unknown';
        $fatalStatus = isset($summary['first_fatal']['status']) ? (int) $summary['first_fatal']['status'] : 0;
        $fatalText = $fatalPath.' (HTTP '.$fatalStatus.')';
    }

    $lines[] = '| '.$label.' | '.$passed.'/'.$total.' | '.$fatalText.' |';
}

$lines[] = '';
$lines[] = '## Details';
$lines[] = '';

foreach ($labels as $label) {
    $resultFile = rtrim($inputDir, '/').'/result-'.$label.'.json';
    $lines[] = '### PHP '.$label;

    if (!is_file($resultFile)) {
        $lines[] = '- Missing result file.';
        $lines[] = '';
        continue;
    }

    $raw = file_get_contents($resultFile);
    $decoded = is_string($raw) ? json_decode($raw, true) : null;

    if (!is_array($decoded) || !isset($decoded['checks']) || !is_array($decoded['checks'])) {
        $lines[] = '- Invalid result JSON.';
        $lines[] = '';
        continue;
    }

    foreach ($decoded['checks'] as $check) {
        $path = isset($check['path']) ? (string) $check['path'] : '/';
        $status = isset($check['status']) ? (int) $check['status'] : 0;
        $pass = isset($check['pass']) && $check['pass'] === true ? 'PASS' : 'FAIL';
        $fatal = isset($check['fatal_detected']) && $check['fatal_detected'] === true ? ' fatal-detected' : '';

        $lines[] = '- '.$pass.' '.$path.' (HTTP '.$status.')'.$fatal;

        if ($pass === 'FAIL' && isset($check['snippet']) && is_string($check['snippet']) && trim($check['snippet']) !== '') {
            $lines[] = '  - Snippet: '.trim($check['snippet']);
        }
    }

    $lines[] = '';
}

$markdown = implode("\n", $lines)."\n";
$dir = dirname($outputPath);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
file_put_contents($outputPath, $markdown);

echo $outputPath, "\n";
