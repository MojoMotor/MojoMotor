<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$defaultOutput = $rootDir.'/reports/dynamic-properties/result-local-php.json';
$defaultReport = $rootDir.'/reports/dynamic-properties/report.md';

$options = getopt('', ['output::', 'report::']);

$outputPath = isset($options['output']) ? (string) $options['output'] : $defaultOutput;
$reportPath = isset($options['report']) ? (string) $options['report'] : $defaultReport;

$scanRoots = [
    $rootDir.'/system/codeigniter/system',
    $rootDir.'/system/mojomotor',
];

/**
 * @return array<int, string>
 */
function collect_php_files(array $roots): array
{
    $files = [];

    foreach ($roots as $root) {
        if (!is_dir($root)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $path => $info) {
            if (!$info->isFile()) {
                continue;
            }

            if (substr($path, -4) !== '.php') {
                continue;
            }

            if (strpos($path, '/third_party/') !== false || strpos($path, '/javascript/') !== false) {
                continue;
            }

            $files[] = $path;
        }
    }

    sort($files);

    return $files;
}

function is_object_operator_token($token): bool
{
    if ($token === '->') {
        return true;
    }

    return is_array($token) && $token[0] === T_OBJECT_OPERATOR;
}

/**
 * @return array{class_name:string,file:string,declared_properties:array<string,bool>,assigned_properties:array<string,array<int,int>>,variable_property_writes:int}
 */
function analyze_class_tokens(array $tokens, string $filePath, int &$index): array
{
    $tokenCount = count($tokens);

    $className = 'anonymous@'.$filePath;
    for ($j = $index + 1; $j < $tokenCount; $j++) {
        $token = $tokens[$j];
        if (is_array($token) && $token[0] === T_STRING) {
            $className = $token[1];
            break;
        }

        if ($token === '{') {
            break;
        }
    }

    while ($index < $tokenCount && $tokens[$index] !== '{') {
        $index++;
    }

    $declaredProperties = [];
    $assignedProperties = [];
    $variablePropertyWrites = 0;
    $braceDepth = 0;
    $inFunctionDepth = 0;
    $functionPending = false;

    for (; $index < $tokenCount; $index++) {
        $token = $tokens[$index];

        if ($token === '{') {
            $braceDepth++;

            if ($functionPending === true && $inFunctionDepth === 0) {
                $inFunctionDepth = $braceDepth;
                $functionPending = false;
            }

            continue;
        }

        if ($token === '}') {
            if ($inFunctionDepth !== 0 && $braceDepth === $inFunctionDepth) {
                $inFunctionDepth = 0;
            }

            $braceDepth--;

            if ($braceDepth <= 0) {
                break;
            }

            continue;
        }

        if (!is_array($token)) {
            continue;
        }

        if ($token[0] === T_FUNCTION && $inFunctionDepth === 0) {
            $functionPending = true;
            continue;
        }

        if ($inFunctionDepth === 0) {
            if ($token[0] === T_VARIABLE) {
                $prevMeaningful = null;
                for ($p = $index - 1; $p >= 0; $p--) {
                    $candidate = $tokens[$p];
                    if (is_array($candidate) && in_array($candidate[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                        continue;
                    }

                    $prevMeaningful = $candidate;
                    break;
                }

                if (is_array($prevMeaningful) && in_array($prevMeaningful[0], [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_VAR, T_STATIC, T_READONLY], true)) {
                    $declaredProperties[substr($token[1], 1)] = true;
                }
            }

            continue;
        }

        if ($token[0] !== T_VARIABLE || $token[1] !== '$this') {
            continue;
        }

        $j = $index + 1;
        while ($j < $tokenCount && is_array($tokens[$j]) && in_array($tokens[$j][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
            $j++;
        }

        if ($j >= $tokenCount || !is_object_operator_token($tokens[$j])) {
            continue;
        }

        $j++;
        while ($j < $tokenCount && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
            $j++;
        }

        if ($j >= $tokenCount) {
            continue;
        }

        if (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
            $k = $j + 1;
            while ($k < $tokenCount && is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) {
                $k++;
            }

            if ($k < $tokenCount && $tokens[$k] === '=') {
                $variablePropertyWrites++;
            }
            continue;
        }

        if (!is_array($tokens[$j]) || $tokens[$j][0] !== T_STRING) {
            continue;
        }

        $property = $tokens[$j][1];
        $line = $tokens[$j][2];

        $k = $j + 1;
        while ($k < $tokenCount && is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) {
            $k++;
        }

        if ($k < $tokenCount && $tokens[$k] === '=') {
            if (!isset($assignedProperties[$property])) {
                $assignedProperties[$property] = [];
            }
            $assignedProperties[$property][] = $line;
        }
    }

    return [
        'class_name' => $className,
        'file' => str_replace($GLOBALS['rootDir'].'/', '', $filePath),
        'declared_properties' => $declaredProperties,
        'assigned_properties' => $assignedProperties,
        'variable_property_writes' => $variablePropertyWrites,
    ];
}

$files = collect_php_files($scanRoots);
$classAnalyses = [];

foreach ($files as $filePath) {
    $source = file_get_contents($filePath);
    if (!is_string($source) || $source === '') {
        continue;
    }

    $tokens = token_get_all($source);
    $tokenCount = count($tokens);

    for ($i = 0; $i < $tokenCount; $i++) {
        $token = $tokens[$i];
        if (!is_array($token) || $token[0] !== T_CLASS) {
            continue;
        }

        $classAnalyses[] = analyze_class_tokens($tokens, $filePath, $i);
    }
}

$candidates = [];
$bridgeClasses = [];
$bridgeClassNames = ['CI_Controller', 'CI_Model'];

foreach ($classAnalyses as $analysis) {
    $undeclaredWrites = [];

    foreach ($analysis['assigned_properties'] as $property => $lines) {
        if (!isset($analysis['declared_properties'][$property])) {
            $undeclaredWrites[$property] = $lines;
        }
    }

    $entry = [
        'class' => $analysis['class_name'],
        'file' => $analysis['file'],
        'undeclared_writes' => $undeclaredWrites,
        'variable_property_writes' => $analysis['variable_property_writes'],
    ];

    if (in_array($analysis['class_name'], $bridgeClassNames, true)) {
        $bridgeClasses[] = $entry;
        continue;
    }

    if (count($undeclaredWrites) > 0 || $analysis['variable_property_writes'] > 0) {
        $candidates[] = $entry;
    }
}

usort($candidates, static function (array $a, array $b): int {
    $ac = count($a['undeclared_writes']) + (int) $a['variable_property_writes'];
    $bc = count($b['undeclared_writes']) + (int) $b['variable_property_writes'];

    if ($ac === $bc) {
        return strcmp($a['file'], $b['file']);
    }

    return $bc <=> $ac;
});

$payload = [
    'timestamp_utc' => gmdate('c'),
    'php_version' => PHP_VERSION,
    'summary' => [
        'scanned_files' => count($files),
        'scanned_classes' => count($classAnalyses),
        'bridge_classes' => count($bridgeClasses),
        'candidate_classes' => count($candidates),
    ],
    'bridge_classes' => $bridgeClasses,
    'candidate_classes' => $candidates,
    'strategy' => [
        'applied' => 'Use #[AllowDynamicProperties] on CI_Controller and CI_Model as a compatibility bridge for PHP 8.2+.',
        'next_phase' => 'Iteratively replace undeclared writes with explicit properties in app-owned classes first, then reassess remaining framework internals.',
    ],
];

$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "Failed to encode dynamic property audit JSON.\n");
    exit(3);
}

$outputDir = dirname($outputPath);
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
file_put_contents($outputPath, $json."\n");

$reportLines = [];
$reportLines[] = '# Dynamic property audit report';
$reportLines[] = '';
$reportLines[] = '- Generated: '.gmdate('Y-m-d H:i:s').' UTC';
$reportLines[] = '- PHP: '.PHP_VERSION;
$reportLines[] = '- Scanned files/classes: '.count($files).'/'.count($classAnalyses);
$reportLines[] = '- Bridge classes: '.count($bridgeClasses);
$reportLines[] = '- Candidate classes for explicit property refactor: '.count($candidates);
$reportLines[] = '';
$reportLines[] = '## Strategy';
$reportLines[] = '';
$reportLines[] = '- Applied bridge: `#[AllowDynamicProperties]` on `CI_Controller` and `CI_Model`.';
$reportLines[] = '- Next phase: prioritize app-owned classes with undeclared writes and add explicit properties.';
$reportLines[] = '';
$reportLines[] = '## Top candidates';
$reportLines[] = '';

if (count($candidates) === 0) {
    $reportLines[] = '- No additional candidate classes detected by static audit rules.';
} else {
    $limit = min(15, count($candidates));
    for ($i = 0; $i < $limit; $i++) {
        $candidate = $candidates[$i];
        $undeclaredCount = count($candidate['undeclared_writes']);
        $variableCount = (int) $candidate['variable_property_writes'];
        $reportLines[] = '- '.$candidate['class'].' ('.$candidate['file'].'): undeclared-writes='.$undeclaredCount.', variable-property-writes='.$variableCount;
    }
}

$reportDir = dirname($reportPath);
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}
file_put_contents($reportPath, implode("\n", $reportLines)."\n");

echo $json, "\n";

exit(0);
