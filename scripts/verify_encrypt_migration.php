<?php

declare(strict_types=1);

$rootDir = dirname(__DIR__);
$defaultOutput = $rootDir.'/reports/encryption/result-local-php.json';
$defaultReport = $rootDir.'/reports/encryption/report.md';

$options = getopt('', ['output::', 'report::']);

$outputPath = isset($options['output']) ? (string) $options['output'] : $defaultOutput;
$reportPath = isset($options['report']) ? (string) $options['report'] : $defaultReport;

if (!defined('BASEPATH')) {
    define('BASEPATH', $rootDir.'/system/codeigniter/system/');
}

class MojoEncryptMigrationConfigStub
{
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function item(string $key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : false;
    }
}

class MojoEncryptMigrationCiStub
{
    public $config;

    public function __construct(string $encryptionKey)
    {
        $this->config = new MojoEncryptMigrationConfigStub([
            'encryption_key' => $encryptionKey,
        ]);
    }
}

$mojoEncryptMigrationCi = new MojoEncryptMigrationCiStub('mojomotor-modernization-encryption-key');

function &get_instance()
{
    global $mojoEncryptMigrationCi;

    return $mojoEncryptMigrationCi;
}

function log_message($level, $message)
{
    return;
}

function show_error($message)
{
    throw new RuntimeException((string) $message);
}

require_once $rootDir.'/system/codeigniter/system/libraries/Encrypt.php';

function mojo_set_encrypt_transport_modes(CI_Encrypt $encrypt, bool $opensslExists, bool $mcryptExists): void
{
    $reflection = new ReflectionClass($encrypt);

    foreach (['_openssl_exists' => $opensslExists, '_mcrypt_exists' => $mcryptExists] as $property => $value) {
        $prop = $reflection->getProperty($property);
        $prop->setValue($encrypt, $value);
    }
}

function mojo_check(bool $condition, string $label, array $context = []): array
{
    return [
        'label' => $label,
        'pass' => $condition,
        'context' => $context,
    ];
}

$checks = [];

$modernEncrypt = new CI_Encrypt();

$rememberPayload = '42:remembertoken:'.(string) (time() + 3600);
$modernToken = $modernEncrypt->encode($rememberPayload);
$decodedModern = is_string($modernToken) ? $modernEncrypt->decode($modernToken) : false;

$checks[] = mojo_check(
    is_string($modernToken) && $modernToken !== '',
    'Modern token is issued',
    ['token_length' => is_string($modernToken) ? strlen($modernToken) : 0]
);

$checks[] = mojo_check(
    $decodedModern === $rememberPayload,
    'Modern token decodes back to original payload',
    ['decoded_matches' => $decodedModern === $rememberPayload]
);

$tamperedToken = is_string($modernToken) && strlen($modernToken) > 12
    ? substr($modernToken, 0, -2).'zz'
    : 'invalid!!';

$tamperedDecoded = $modernEncrypt->decode($tamperedToken);
$checks[] = mojo_check(
    $tamperedDecoded !== $rememberPayload,
    'Tampered token does not decode to original payload',
    ['decode_result_type' => gettype($tamperedDecoded), 'matches_original' => $tamperedDecoded === $rememberPayload]
);

$xorLegacyEncrypt = new CI_Encrypt();
mojo_set_encrypt_transport_modes($xorLegacyEncrypt, false, false);

$xorLegacyToken = $xorLegacyEncrypt->encode($rememberPayload);
$decodedXorLegacy = is_string($xorLegacyToken) ? $modernEncrypt->decode($xorLegacyToken) : false;

$checks[] = mojo_check(
    is_string($xorLegacyToken) && $xorLegacyToken !== '',
    'Legacy XOR token fixture is generated',
    ['token_length' => is_string($xorLegacyToken) ? strlen($xorLegacyToken) : 0]
);

$checks[] = mojo_check(
    $decodedXorLegacy === $rememberPayload,
    'Legacy XOR token can be decoded by current runtime',
    ['decoded_matches' => $decodedXorLegacy === $rememberPayload]
);

$legacyUpgradeResult = $modernEncrypt->encode_from_legacy((string) $xorLegacyToken);
$checks[] = mojo_check(
    $legacyUpgradeResult === false,
    'Legacy mcrypt re-encode path unavailable without mcrypt (expected)',
    ['encode_from_legacy_result' => $legacyUpgradeResult === false ? 'false' : 'non-false']
);

$passed = 0;
foreach ($checks as $check) {
    if ($check['pass'] === true) {
        $passed++;
    }
}

$migrationAction = 'Force re-authentication for any pre-modern mcrypt-derived remember-me/session tokens after deployment; new tokens are re-issued automatically on successful login/session write.';

$payload = [
    'timestamp_utc' => gmdate('c'),
    'php_version' => PHP_VERSION,
    'summary' => [
        'passed' => $passed,
        'total' => count($checks),
    ],
    'checks' => $checks,
    'migration_action' => $migrationAction,
];

$json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
if ($json === false) {
    fwrite(STDERR, "Failed to encode encryption migration result JSON.\n");
    exit(3);
}

$outputDir = dirname($outputPath);
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
file_put_contents($outputPath, $json."\n");

$status = $passed === count($checks) ? 'PASS' : 'FAIL';

$reportLines = [];
$reportLines[] = '# Encryption migration verification report';
$reportLines[] = '';
$reportLines[] = '- Generated: '.gmdate('Y-m-d H:i:s').' UTC';
$reportLines[] = '- PHP: '.PHP_VERSION;
$reportLines[] = '- Result: '.$status.' ('.$passed.'/'.count($checks).')';
$reportLines[] = '';
$reportLines[] = '## Checks';
$reportLines[] = '';

foreach ($checks as $check) {
    $reportLines[] = '- '.($check['pass'] ? 'PASS' : 'FAIL').' '.$check['label'];
}

$reportLines[] = '';
$reportLines[] = '## Migration behavior';
$reportLines[] = '';
$reportLines[] = '- '.$migrationAction;
$reportLines[] = '- Legacy XOR token decode remains functional in current runtime.';
$reportLines[] = '- `encode_from_legacy()` still requires mcrypt and is expected to return `FALSE` when mcrypt is unavailable.';

$reportDir = dirname($reportPath);
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

file_put_contents($reportPath, implode("\n", $reportLines)."\n");

echo $json, "\n";

exit($passed === count($checks) ? 0 : 2);
