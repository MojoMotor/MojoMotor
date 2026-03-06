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

## Remaining tasks

### 1) Runtime baseline and smoke checks (in progress)

- **Goal:** Add runtime validation (beyond static lint) on supported PHP versions.
- **Deliverable:** Repeatable smoke test commands for `/`, `/setup`, login, and a sample rendered page on PHP 8.1/8.2/8.3.
- **Status:**
  - ✅ Smoke harness implemented (`scripts/smoke-matrix.sh`, `scripts/smoke_check.php`, `scripts/render_smoke_report.php`).
  - ✅ Report output implemented (`reports/smoke/report.md` + JSON results).
  - ✅ Local fallback run completed in this environment (`result-local-php.json`, all 4 checks passed).
  - ⏳ Pending: run full Docker matrix for `8.1/8.2/8.3` on a Docker-capable host and commit those versioned result files.
- **Prompt:**
  - "Create a lightweight smoke-test harness (scripts or docker-compose) that exercises `/`, `/setup`, admin login, and one page render on PHP 8.1, 8.2, and 8.3. Emit a markdown report with pass/fail and first runtime error per version."

### 2) Migrate database usage from `mysql` driver to `mysqli` (or PDO)

- **Goal:** Remove dependency on deleted `ext/mysql`.
- **Deliverable:** Config defaults and framework path that no longer depend on `mysql_*`.
- **Status:**
  - ✅ Completed for installer/config defaults.
  - Updated default `dbdriver` to `mysqli` in config prototypes and `database.php`.
  - Updated setup wizard DB type selection to submit `mysqli`.
  - Updated setup JS hooks and installer language text to present MySQLi.
- **Prompt:**
  - "Migrate DB config defaults and setup/install flow away from `mysql` to `mysqli` while preserving upgrade behavior for existing installs. Ensure installer UI/options and language strings no longer encourage deprecated drivers."

### 3) Finalize encryption migration behavior

- **Goal:** Ensure encrypted token interoperability and migration behavior is explicitly verified.
- **Deliverable:** Documented compatibility strategy + tests for remember-me/session token read/write.
- **Status:**
  - ✅ Added repeatable runtime verification harness: `scripts/verify_encrypt_migration.php`.
  - ✅ Added composer entrypoint: `composer verify:encrypt`.
  - ✅ Generated result artifacts: `reports/encryption/result-local-php.json` and `reports/encryption/report.md`.
  - ✅ Verified modern token issue/decode, tamper rejection, and legacy XOR decode compatibility.
  - ✅ Documented expected migration behavior where legacy `encode_from_legacy()` requires mcrypt and falls back to forced re-auth for pre-modern mcrypt-derived tokens.
- **Prompt:**
  - "Verify and document `CI_Encrypt` runtime behavior on PHP 8.2: include tests for newly issued tokens, legacy token handling, and re-issue flow. If legacy token decode is not possible without mcrypt, add explicit forced-reauth migration behavior and release notes."

### 4) Address PHP 8.2 dynamic property deprecations strategically

- **Goal:** Reduce warning noise and future breakage risk.
- **Deliverable:** Decision + implementation: explicit properties, `#[AllowDynamicProperties]` bridge, or targeted class refactors.
- **Status:**
  - ✅ Applied compatibility bridge on framework base classes: `#[AllowDynamicProperties]` on `CI_Controller` and `CI_Model`.
  - ✅ Added repeatable audit harness: `scripts/audit_dynamic_properties.php` + composer entrypoint `composer audit:dynamic-properties`.
  - ✅ Generated audit artifacts: `reports/dynamic-properties/result-local-php.json` and `reports/dynamic-properties/report.md`.
  - ✅ Captured deeper-refactor candidates from static audit (36 total), including app-owned classes: `Utilities`, `Mojomotor_Config`, `CI_Auth`, `Mojomotor_parser`, and `Mojomotor_pagination`.
  - ✅ Adopted phased strategy: keep bridge for runtime stability, then incrementally add explicit properties to app-owned candidates first.
- **Prompt:**
  - "Audit classes for dynamic property creation and implement a phased strategy: add declared properties where practical, use temporary bridging only where necessary, and document classes requiring deeper refactor. Keep behavior unchanged."

### 5) Harden signatures and type expectations incrementally

- **Goal:** Prevent PHP 8 strictness issues from surfacing at runtime.
- **Deliverable:** Incremental fixes for parameter defaults, return expectations, and string/array handling edge cases.
- **Status:**
  - ✅ Applied targeted null/false string guards in app-owned paths (`system/mojomotor`) without broad rewrites.
  - ✅ Hardened installer/welcome base URL detection to avoid null `REQUEST_URI` handling and non-strict `strpos()` behavior.
  - ✅ Hardened remember-me/contact parsing flows for non-string decrypt output before string operations (`strpos`, `explode`, email validation).
  - ✅ Corrected Mojo parser comment-removal `strpos` edge case (`0` offset falsey bug).
  - ✅ Validation: syntax checks passed on all touched files; `composer lint:style` and `composer lint:compat` both pass.
- **Prompt:**
  - "Run targeted PHP 8 hardening on app code (`system/mojomotor`) first: fix signature/order/deprecation issues flagged by runtime and static tools, add guards for null/false handling in string operations, and avoid broad rewrites."

### 6) Create regression smoke tests for critical CMS flows

- **Goal:** Ensure modernization doesn’t break core features.
- **Deliverable:** Basic test coverage for setup, login, page rendering, and admin save operations.
- **Prompt:**
  - "Add lightweight regression tests (or scriptable smoke tests) for setup wizard, admin login, page render, and page save/update. Focus on high-value workflows and keep test harness simple enough for legacy project constraints."

### 7) Rollout and release checklist

- **Goal:** Ship safely with known risk log.
- **Deliverable:** Release checklist and rollback notes.
- **Prompt:**
  - "Create a release checklist for the PHP modernization effort including backup/rollback steps, DB backup reminder, config diffs, post-deploy smoke checks, and known limitations list."

---

## Current completed foundation

- Dev tooling is in place (`composer.json`, `phpcs.xml.dist`, lock file, lint scripts).
- `lint:style` currently passes with project-configured policy.
- `lint:compat` currently passes with configured scan scope.
- Major PHP 8 blockers were addressed in core (removed API usage, short-tag policy tuning, encryption modernization path, and broad `each()` cleanup in patched areas).

---

## Suggested execution order

1. Tasks 1–3 (runtime validation + DB + encryption migration completion)
2. Tasks 4–5 (deprecation/type hardening)
3. Tasks 6–7 (regression coverage and release readiness)
