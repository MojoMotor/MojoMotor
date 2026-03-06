# MojoMotor PHP Modernization Plan

## Scope and targets

- **Primary runtime target:** PHP 8.2 (with validation on 8.3).
- **Secondary compatibility check:** PHP 8.1.
- **Current baseline found in repo:** CodeIgniter 2.0.1-era code and patterns.

## Key compatibility hotspots found

- Bundled framework core is very old (`CI_VERSION` is `2.0.1`).
- Legacy MySQL extension usage exists (`mysql_*`) and default DB config uses `dbdriver = mysql`.
- Removed functions/APIs are used (`each()`, `set_magic_quotes_runtime()`, `get_magic_quotes_gpc()`).
- Legacy encryption path relies on `mcrypt_*` and MCRYPT constants.
- Dynamic property-heavy patterns are common (expected deprecations/noise in PHP 8.2).

---

## Task list with implementation prompts

### 1) Establish a repeatable compatibility baseline

- **Goal:** Reproduce current failures on modern PHP so progress is measurable.
- **Deliverable:** A documented command matrix to run app smoke checks on PHP 8.1/8.2/8.3.
- **Prompt:**
  - "Create a minimal compatibility test harness for this project: add scripts (or Docker commands) to run smoke checks for `/`, `/setup`, and a sample page route across PHP 8.1, 8.2, and 8.3. Output a markdown report with pass/fail and first fatal error per version."

### 2) Add Composer tooling for linting and compatibility scanning

- **Goal:** Introduce modern static checks without changing runtime behavior.
- **Deliverable:** `composer.json` with PHPCS tooling scripts.
- **Prompt:**
  - "Add a `composer.json` for dev tooling only (no runtime migration yet) with `squizlabs/php_codesniffer`, `phpcompatibility/php-compatibility`, and scripts for `lint:style` and `lint:compat`. Keep dependencies minimal and compatible with PHP 8.2 tooling."

### 3) Set up PHPCS to match legacy CodeIgniter-style formatting

- **Goal:** Enforce existing style (tabs + legacy CI conventions), not PSR-12 rewrites.
- **Deliverable:** `phpcs.xml.dist` tailored to this codebase.
- **Prompt:**
  - "Create `phpcs.xml.dist` that preserves this codebase's legacy CodeIgniter-style formatting (tab indentation, no forced PSR-12 conversions, avoid strict modern naming sniffs). Scope checks to `system/mojomotor` first, with optional checks for `system/codeigniter/system` in a separate path."

### 4) Add a separate PHPCompatibility pass for PHP 8.2/8.3

- **Goal:** Detect incompatible APIs quickly and keep style checks separate.
- **Deliverable:** PHPCS command/script using `PHPCompatibility` and `testVersion`.
- **Prompt:**
  - "Add a compatibility scan command using PHPCS + PHPCompatibility with `testVersion=8.2-8.3`, and include excludes for generated/cache/third-party directories. Output should be CI-friendly (non-interactive, clear exit code)."

### 5) Remove/replace removed PHP functions in core path

- **Goal:** Eliminate immediate hard-fatal APIs.
- **Deliverable:** Replacements for `each()`, magic quotes runtime calls, and related patterns.
- **Prompt:**
  - "Refactor all `each()` usage to `foreach`/iterator-safe equivalents in framework and app code, preserving behavior. Remove dead magic-quotes handling (`set_magic_quotes_runtime`, `get_magic_quotes_gpc`) with minimal behavior change. Produce a changelog of touched files and rationale."

### 6) Migrate database usage from `mysql` driver to `mysqli` (or PDO)

- **Goal:** Remove dependency on deleted `ext/mysql`.
- **Deliverable:** Config defaults and framework path that no longer depend on `mysql_*`.
- **Prompt:**
  - "Migrate DB config defaults and setup/install flow away from `mysql` to `mysqli` while preserving upgrade behavior for existing installs. Ensure installer UI/options and language strings no longer encourage deprecated drivers."

### 7) Replace legacy encryption implementation

- **Goal:** Remove `mcrypt` dependency while preserving login/session behavior.
- **Deliverable:** Secure replacement strategy for encrypt/decrypt and remember-me tokens.
- **Prompt:**
  - "Audit `CI_Encrypt` usage in this project and implement a PHP 8-compatible replacement strategy (OpenSSL/Sodium or framework-native approach). Preserve ability to read legacy tokens where feasible, with a migration path to re-issue modern tokens."

### 8) Address PHP 8.2 dynamic property deprecations strategically

- **Goal:** Reduce warning noise and future breakage risk.
- **Deliverable:** Decision + implementation: explicit properties, `#[AllowDynamicProperties]` bridge, or targeted class refactors.
- **Prompt:**
  - "Audit classes for dynamic property creation and implement a phased strategy: add declared properties where practical, use temporary bridging only where necessary, and document classes requiring deeper refactor. Keep behavior unchanged."

### 9) Harden signatures and type expectations incrementally

- **Goal:** Prevent PHP 8 strictness issues from surfacing at runtime.
- **Deliverable:** Incremental fixes for parameter defaults, return expectations, and string/array handling edge cases.
- **Prompt:**
  - "Run targeted PHP 8 hardening on app code (`system/mojomotor`) first: fix signature/order/deprecation issues flagged by runtime and static tools, add guards for null/false handling in string operations, and avoid broad rewrites."

### 10) Create regression smoke tests for critical CMS flows

- **Goal:** Ensure modernization doesn’t break core features.
- **Deliverable:** Basic test coverage for setup, login, page rendering, and admin save operations.
- **Prompt:**
  - "Add lightweight regression tests (or scriptable smoke tests) for setup wizard, admin login, page render, and page save/update. Focus on high-value workflows and keep test harness simple enough for legacy project constraints."

### 11) Rollout and release checklist

- **Goal:** Ship safely with known risk log.
- **Deliverable:** Release checklist and rollback notes.
- **Prompt:**
  - "Create a release checklist for the PHP modernization effort including backup/rollback steps, DB backup reminder, config diffs, post-deploy smoke checks, and known limitations list."

---

## PHPCS setup guide (legacy CodeIgniter-style)

### 1) Install tooling

```bash
composer require --dev squizlabs/php_codesniffer phpcompatibility/php-compatibility dealerdirect/phpcodesniffer-composer-installer
```

### 2) Add scripts to `composer.json`

```json
{
  "scripts": {
    "lint:style": "phpcs -p --standard=phpcs.xml.dist",
    "lint:compat": "phpcs -p --standard=PHPCompatibility --runtime-set testVersion 8.2-8.3 system/mojomotor system/codeigniter/system"
  }
}
```

### 3) Create `phpcs.xml.dist` tuned for current style

```xml
<?xml version="1.0"?>
<ruleset name="MojoMotor-Legacy-CI-Style">
  <description>Legacy CodeIgniter-style checks without forcing PSR-12 rewrites.</description>

  <arg name="basepath" value="."/>
  <arg name="extensions" value="php"/>
  <arg value="sp"/>
  <arg name="parallel" value="4"/>
  <config name="tab_width" value="4"/>

  <file>system/mojomotor</file>

  <!-- Optional: include framework core once noise is manageable -->
  <!-- <file>system/codeigniter/system</file> -->

  <exclude-pattern>*/third_party/*</exclude-pattern>
  <exclude-pattern>*/logs/*</exclude-pattern>
  <exclude-pattern>*/cache/*</exclude-pattern>

  <!-- Keep a permissive baseline close to old CI code style -->
  <rule ref="Generic.Files.ByteOrderMark"/>
  <rule ref="Generic.PHP.DisallowShortOpenTag"/>
  <rule ref="Generic.WhiteSpace.DisallowSpaceIndent"/>
  <rule ref="Squiz.WhiteSpace.SuperfluousWhitespace"/>

  <!-- Avoid modern-style churn in legacy code -->
  <exclude name="PSR1"/>
  <exclude name="PSR12"/>
  <exclude name="Squiz.Classes.ValidClassName"/>
  <exclude name="Squiz.NamingConventions.ValidVariableName"/>
  <exclude name="Generic.PHP.LowerCaseConstant"/>
</ruleset>
```

### 4) Recommended workflow

- Run `composer lint:style` first for formatting consistency.
- Run `composer lint:compat` second for PHP 8.2/8.3 blockers.
- Fix compatibility blockers before broad style cleanups.

---

## Suggested execution order

1. Tasks 1–4 (tooling and measurable baseline)
2. Tasks 5–7 (hard blockers: removed APIs, DB, encryption)
3. Tasks 8–10 (deprecation hardening and regression coverage)
4. Task 11 (release hardening)
