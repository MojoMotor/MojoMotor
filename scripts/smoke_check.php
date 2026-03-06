<?php

declare(strict_types=1);

$options = getopt('', ['base-url:', 'output:', 'paths::']);

$baseUrl = isset($options['base-url']) ? rtrim((string) $options['base-url'], '/') : 'http://127.0.0.1:8080';
$outputPath = isset($options['output']) ? (string) $options['output'] : '';
$pathsCsv = isset($options['paths']) ? (string) $options['paths'] : '/,/setup,/index.php/admin/login,/index.php/page/content';

$paths = array_values(array_filter(array_map('trim', explode(',', $pathsCsv)), static function (string $path): bool {
    return $path !== '';
}));

$fatalPattern = '/(Fatal error|Uncaught|Parse error|TypeError|\bError:\b)/i';
$results = [];
$firstFatal = null;

foreach ($paths as $path) {
    $url = $baseUrl.$path;

    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 12,
            'method' => 'GET',
            'header' => "User-Agent: MojoMotor-Smoke/1.0\r\n",
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $headers = $http_response_header ?? [];

    $statusLine = isset($headers[0]) ? $headers[0] : 'HTTP/0 000 NO_RESPONSE';
    preg_match('/HTTP\/\S+\s+(\d{3})/', $statusLine, $statusMatch);
    $statusCode = isset($statusMatch[1]) ? (int) $statusMatch[1] : 0;

    $bodyText = is_string($body) ? $body : '';
    $fatalMatched = preg_match($fatalPattern, $bodyText) === 1;

    $isPass = $statusCode > 0 && $statusCode < 500 && ! $fatalMatched;

    $snippet = trim(substr(preg_replace('/\s+/', ' ', $bodyText), 0, 220));

    if ($fatalMatched && $firstFatal === null) {
        $firstFatal = [
            'path' => $path,
            'status' => $statusCode,
            'snippet' => $snippet,
        ];
    }

    $results[] = [
        'path' => $path,
        'url' => $url,
        'status' => $statusCode,
        'pass' => $isPass,
        'fatal_detected' => $fatalMatched,
        'snippet' => $snippet,
    ];
}

$passed = 0;
foreach ($results as $row) {
    if ($row['pass'] === true) {
        $passed++;
    }
}

$payload = [
    'timestamp_utc' => gmdate('c'),
    'base_url' => $baseUrl,
    'checks' => $results,
    'summary' => [
        'passed' => $passed,
        'total' => count($results),
        'first_fatal' => $firstFatal,
    ],
];

$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "Failed to encode smoke check JSON.\n");
    exit(3);
}

if ($outputPath !== '') {
    $dir = dirname($outputPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($outputPath, $json."\n");
}

echo $json, "\n";

exit($passed === count($results) ? 0 : 2);
