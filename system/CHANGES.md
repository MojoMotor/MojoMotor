# CodeIgniter Modifications

This file documents every change made to the vendored CodeIgniter files in
`system/codeigniter/system/`. The base version is **CI 2.2.6**.

All modifications are required for PHP 8.x compatibility unless noted otherwise.
When upgrading CI in the future, re-apply these patches to the new base.

---

## Changes applied on top of CI 2.2.6 (2026-03-19)

### core/CodeIgniter.php
- Removed `magic_quotes_runtime` `set_magic_quotes_runtime()` call — removed in PHP 8.
- Removed `safe_mode` `ini_get()` check from `set_time_limit()` block — `safe_mode`
  was removed in PHP 5.4.

### core/Common.php
- Changed `function &load_class()` reference return to `function load_class()`
  to silence PHP 8.1 deprecation warning.
- Added default `$message = ''` to `log_message()` wrapper to fix "optional before
  required parameter" deprecation introduced in PHP 8.0.

### core/Controller.php
- Added `#[AllowDynamicProperties]` attribute to `CI_Controller`.

### core/Exceptions.php
- Removed `E_STRICT` from the `$levels` map — constant removed in PHP 8.0.

### core/Input.php
- Removed `get_magic_quotes_gpc()` code path (removed in PHP 8.0).
- Fixed `valid_ip()`: changed default flag from `''` to `0` and used `!== FALSE`
  comparison for FILTER_VALIDATE_IP to avoid false negatives.
- Added explicit `$security` and `$uni` property declarations.

### core/Loader.php
- Added `#[AllowDynamicProperties]` attribute to `CI_Loader`.

### core/Model.php
- Added `#[AllowDynamicProperties]` attribute to `CI_Model`.

### core/Output.php
- CI 2.2.6 already added explicit property declarations with `protected` visibility.
  No additional patch needed.

### core/Router.php
- CI 2.2.6 already has `var $uri;` property declaration. No patch needed.

### core/Security.php
- Replaced all `each()` calls (removed PHP 8.0) with `foreach`.
- Replaced `preg_replace()` with `/e` modifier (removed PHP 7.0) with
  `preg_replace_callback()` in XSS cleaning routines.
- File copied wholesale from patched 2.0.1 baseline.

### core/URI.php
- CI 2.2.6 already has `var $config;` property declaration. No patch needed.

### core/Utf8.php
- No patch needed; CI 2.2.6 version is functionally equivalent to patched 2.0.1.

### database/DB_cache.php
- CI 2.2.6 already uses `__construct()`. No patch needed.

### database/DB_driver.php
- Added explicit `$_trans_failure` property declaration.
- Added `_db_connection_diagnostics()`, `_db_parse_host()`, `_db_extension_status()`,
  `_db_connection_error_text()`, `_db_diag_label()`, and `_db_escape_error_value()`
  helper methods for structured DB connection error reporting (used by setup wizard).
- Wired `_db_connection_diagnostics()` into the `db_unable_to_connect` error path.
- Changed `debug_backtrace()` to `debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)` for
  reduced memory usage.

### database/DB_forge.php
- Added explicit `$db` property declaration.
- CI 2.2.6 already uses `__construct()`. No patch needed for constructor rename.

### database/DB_utility.php
- CI 2.2.6 already uses `__construct()`. No patch needed.

### database/drivers/mysqli/mysqli_driver.php
- Added `mysqli_report(MYSQLI_REPORT_OFF)` at top of `db_connect()` to prevent
  PHP 8.1 implicit exception promotion from mysqli.
- Wrapped `mysqli_connect()` in `try/catch(Exception)` for clean failure handling.
- Fixed `_error_message()` and `_error_number()` to fall back to
  `mysqli_connect_error()` / `mysqli_connect_errno()` when `$conn_id` is not
  a valid object (e.g. during failed connection).
- CI 2.2.6 already removed `mysql_escape_string()` fallback. No patch needed there.

### database/drivers/sqlite/sqlite_driver.php
- Added `function_exists()` guards for `sqlite_popen`, `sqlite_open`, and
  `sqlite_libversion` — the legacy `ext/sqlite` extension is absent in PHP 8.
- File copied wholesale from patched 2.0.1 baseline.

### language/english/db_lang.php
- Added connection-diagnostics language strings: `db_connection_details`,
  `db_error_stage`, `db_error_driver`, `db_error_host`, `db_error_port`,
  `db_error_socket`, `db_error_database`, `db_error_username`,
  `db_error_extension`, `db_error_message`.

### libraries/Calendar.php
- Added explicit `$temp` property declaration.

### libraries/Driver.php
- Added `#[AllowDynamicProperties]` attribute to `CI_Driver_Library`.
- CI 2.2.6 already uses `protected $lib_name` (instance property, not static).
  No patch needed for the static→instance change.

### libraries/Email.php
- CI 2.2.6 already uses `__construct()`. No patch needed.

### libraries/Encrypt.php
- **Complete rewrite** to use OpenSSL as the primary encryption backend.
  `mcrypt` was removed in PHP 7.2. The public API (`encode`/`decode`/
  `get_key`/`set_key`) is unchanged.
- Uses `AES-256-CBC` via `openssl_encrypt()` / `openssl_decrypt()`.
- Transparent migration: existing mcrypt-encoded payloads are detected and
  decoded via a fallback path on first read, then re-encoded with OpenSSL.
- File copied wholesale from patched 2.0.1 baseline.

### libraries/Form_validation.php
- CI 2.2.6 already uses `protected` property declarations. No additional patch needed.

### libraries/Image_lib.php
- Added explicit `$dest_image` and `$wm_use_opacity` property declarations.
- CI 2.2.6 already uses `__construct()`. No patch needed for constructor rename.

### libraries/Javascript.php
- Added explicit `$CI` and `$js` property declarations.

### libraries/Log.php
- Removed default `'error'` value from `$level` parameter in `write_log()`.
  The `log_message()` wrapper in `Common.php` provides the default; the
  implementation now requires callers to be explicit.

### libraries/Pagination.php
- Added explicit `$CI` property declaration.
- CI 2.2.6 already uses `__construct()`. No patch needed for constructor rename.

### libraries/Profiler.php
- Replaced `$this->_compile_{$section}` curly-brace variable syntax (invalid
  PHP 8.2) with `$this->{'_compile_'.$section}` in section-enable and
  section-output loops.

### libraries/Session.php
- CI 2.2.6 already has explicit property declarations. No additional patch needed.

### libraries/Table.php
- CI 2.2.6 already has `var $temp = NULL;` property declaration. No patch needed.

### libraries/Upload.php
- CI 2.2.6 already has explicit property declarations. No additional patch needed.

### libraries/Xmlrpc.php
- Replaced `each()` / `list()` calls with `foreach` / `current()`.
- File copied wholesale from patched 2.0.1 baseline.

### libraries/Xmlrpcs.php
- Added explicit `$debug` and `$xss_clean` property declarations.
- Replaced `${err}` string interpolation with `{$err}` (PHP 8.2 syntax).
- Replaced `each()` calls with `foreach` / `current()`.
- File copied wholesale from patched 2.0.1 baseline.

### helpers/text_helper.php
- Replaced `preg_replace()` with `/e` modifier in `word_censor()` with
  `preg_replace_callback()` (the `/e` modifier was removed in PHP 7.0).
