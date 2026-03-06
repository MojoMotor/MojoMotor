<?php

declare(strict_types=1);

$options = getopt('', ['base-url:', 'output:']);

$baseUrl = isset($options['base-url']) ? rtrim((string) $options['base-url'], '/') : 'http://127.0.0.1:8080';
$outputPath = isset($options['output']) ? (string) $options['output'] : '';

$fatalPattern = '/(Fatal error|Uncaught|Parse error|TypeError|ValueError|\bError:\b)/i';

$checks = [
    [
        'name' => 'homepage_get',
        'method' => 'GET',
        'path' => '/',
    ],
    [
        'name' => 'setup_get',
        'method' => 'GET',
        'path' => '/setup',
    ],
    [
        'name' => 'admin_login_get',
        'method' => 'GET',
        'path' => '/index.php/admin/login',
    ],
    [
        'name' => 'page_render_get',
        'method' => 'GET',
        'path' => '/index.php/page/content',
    ],
    [
        'name' => 'admin_login_post_invalid',
        'method' => 'POST',
        'path' => '/index.php/admin/login/process',
        'allow_5xx_without_fatal' => true,
        'body' => [
            'email' => 'invalid@example.invalid',
            'password' => 'bad-password',
            'remember_me' => 'no',
        ],
    ],
    [
        'name' => 'admin_pages_update_post_unauth',
        'method' => 'POST',
        'path' => '/index.php/admin/pages/update',
        'allow_5xx_without_fatal' => true,
        'body' => [
            'page_title' => 'Smoke Test Page',
            'url_title' => 'smoke-test-page',
            'layout_id' => '1',
            'include_in_page_list' => 'y',
            'meta_keywords' => '',
            'meta_description' => '',
        ],
    ],
];

$results = [];
$firstFatal = null;

foreach ($checks as $check) {
    $url = $baseUrl.$check['path'];
    $headers = [
        'User-Agent: MojoMotor-Regression/1.0',
    ];

    $method = $check['method'];
    $content = '';

    if ($method === 'POST') {
        $content = http_build_query($check['body'] ?? []);
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'Content-Length: '.strlen($content);
    }

    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true,
            'timeout' => 15,
            'method' => $method,
            'header' => implode("\r\n", $headers)."\r\n",
            'content' => $content,
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $responseHeaders = $http_response_header ?? [];

    $statusLine = isset($responseHeaders[0]) ? $responseHeaders[0] : 'HTTP/0 000 NO_RESPONSE';
    preg_match('/HTTP\/\S+\s+(\d{3})/', $statusLine, $statusMatch);
    $statusCode = isset($statusMatch[1]) ? (int) $statusMatch[1] : 0;

    $bodyText = is_string($body) ? $body : '';
    $fatalDetected = preg_match($fatalPattern, $bodyText) === 1;
    $allow5xxWithoutFatal = isset($check['allow_5xx_without_fatal']) && $check['allow_5xx_without_fatal'] === true;

    $pass = $statusCode > 0 && ! $fatalDetected && ($statusCode < 500 || $allow5xxWithoutFatal);
    $snippet = trim(substr(preg_replace('/\s+/', ' ', $bodyText), 0, 220));

    if ($fatalDetected && $firstFatal === null) {
        $firstFatal = [
            'name' => $check['name'],
            'path' => $check['path'],
            'status' => $statusCode,
            'snippet' => $snippet,
        ];
    }

    $results[] = [
        'name' => $check['name'],
        'method' => $method,
        'path' => $check['path'],
        'status' => $statusCode,
        'pass' => $pass,
        'fatal_detected' => $fatalDetected,
        'allow_5xx_without_fatal' => $allow5xxWithoutFatal,
        'snippet' => $snippet,
    ];
}

$passed = 0;
foreach ($results as $result) {
    if ($result['pass'] === true) {
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
    fwrite(STDERR, "Failed to encode regression check JSON.\n");
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
