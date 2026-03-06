# CodeIgniter Update Plan (MojoMotor)

## Goal

Upgrade MojoMotor from CodeIgniter 2.0.1 to a modern, supportable baseline with minimal breakage.

Recommended target path:

1. CI 2.0.1 -> CI 2.2.6 (short compatibility bridge)
2. CI 2.2.6 -> CI 3.1.x (stabilize)
3. Optional later: CI 3.1.x -> CI 4.x (separate project)

Do not attempt a direct CI2 -> CI4 migration in one pass unless rewrite-level risk is acceptable.

## Why This Is High Risk Here

- Core framework version is very old: `system/codeigniter/system/core/CodeIgniter.php` (`CI_VERSION = 2.0.1`).
- Front controller uses CI2-era bootstrap constants and flow: `index.php`.
- App extends/replaces CI core classes:
  - `system/mojomotor/core/Mojomotor_Config.php`
  - `system/mojomotor/core/Mojomotor_Controller.php`
  - `system/mojomotor/core/Mojomotor_Lang.php`
  - `system/mojomotor/core/Mojomotor_Security.php`
- Legacy encryption API is used in app logic:
  - `system/mojomotor/libraries/Auth.php`
  - `system/mojomotor/libraries/Mojomotor_Session.php`
  - `system/mojomotor/libraries/Mojomotor_parser/drivers/Mojomotor_parser_contact.php`

## Effort and Risk Estimate

- CI2 -> CI3: medium-high risk, ~2-6 weeks depending on parity needs and test throughput.
- CI2 -> CI4 directly: high/very high risk, ~2-4+ months and partial rewrite behavior.

## Non-Negotiable Safety Rules

- No in-place upgrade on production.
- One migration branch only: `ci3-upgrade`.
- Keep current regression/smoke scripts as release gates.
- Every phase ends with a runnable checkpoint and rollback tag.

## Preflight Baseline (Day 0)

1. Create a full behavior baseline on current branch.
2. Archive before/after outputs for reproducibility.

Commands:

```bash
composer lint:style
composer lint:compat
composer verify:encrypt
composer audit:dynamic-properties
composer test:regression
bash scripts/smoke-matrix.sh
```

Artifacts to keep:

- `reports/smoke/report.md`
- `reports/regression/report.md`
- `reports/encryption/report.md`
- `reports/dynamic-properties/report.md`

Rollback tag:

- `pre-ci-upgrade-baseline`

## Phase 1: CI 2.0.1 -> CI 2.2.6 Bridge

Objective: reduce delta before CI3 by moving to latest CI2 first.

### 1.1 Vendor in CI 2.2.6 system core

- Replace only framework internals under `system/codeigniter/system/`.
- Do not modify `system/mojomotor/` app files yet except compatibility fixes.

### 1.2 Reconcile bootstrap/front controller

Files:

- `index.php`

Actions:

- Update CI2 bootstrap conventions to match 2.2.6 sample while preserving current app paths.
- Verify constants and path resolution still point to:
  - app: `system/mojomotor/`
  - framework: `system/codeigniter/system/`

### 1.3 Diff and patch app-level core extensions

Files:

- `system/mojomotor/core/Mojomotor_Config.php`
- `system/mojomotor/core/Mojomotor_Controller.php`
- `system/mojomotor/core/Mojomotor_Lang.php`
- `system/mojomotor/core/Mojomotor_Security.php`

Actions:

- Compare signatures/properties against CI 2.2.6 parent classes.
- Update overrides to match parent method signatures and return contracts.
- Remove assumptions tied to CI 2.0.1 internals.

### 1.4 Gate checks

Run:

```bash
composer test:regression
bash scripts/smoke-matrix.sh
composer verify:encrypt
```

Checkpoint tag:

- `ci-2.2.6-stable`

## Phase 2: CI 2.2.6 -> CI 3.1.x Upgrade

Objective: move to maintained CI3 line with minimal application behavior drift.

### 2.1 Replace framework system folder with CI3

- Replace `system/codeigniter/system/` with CI3 system contents.
- Keep app in `system/mojomotor/`.

### 2.2 Update bootstrap for CI3

Files:

- `index.php`

Actions:

- Remove deprecated CI2 bootstrap usage (`EXT` constants and old include styles).
- Align with CI3 front controller structure, preserving custom app/system paths.

### 2.3 Update configuration files to CI3 format

Files (high priority):

- `system/mojomotor/config/config.php`
- `system/mojomotor/config/database.php`
- `system/mojomotor/config/autoload.php`
- `system/mojomotor/config/routes.php`
- `system/mojomotor/config/hooks.php`

Actions:

- Merge in CI3 defaults and keep app-specific settings.
- Re-validate hook and subclass prefix behavior.

### 2.4 Update loader/library patterns most likely to break

Files (sample hotspots):

- `system/mojomotor/controllers/setup.php`
- `system/mojomotor/controllers/admin/*.php`
- `system/mojomotor/libraries/Mojomotor_parser/Mojomotor_parser.php`
- `system/mojomotor/libraries/Mojomotor_addons.php`

Actions:

- Fix loader assumptions that changed between CI2 and CI3.
- Validate parser driver loading and addon loading paths.

### 2.5 Replace deprecated Encrypt usage strategy

Files:

- `system/mojomotor/libraries/Auth.php`
- `system/mojomotor/libraries/Mojomotor_Session.php`
- `system/mojomotor/libraries/Mojomotor_parser/drivers/Mojomotor_parser_contact.php`
- `system/mojomotor/libraries/Mojomotor_parser/drivers/Mojomotor_parser_cookie_consent.php`

Actions:

- CI3 deprecates old Encrypt semantics; define compatibility shim or migrate calls.
- Preserve ability to read legacy encrypted payloads during transition window.

Recommendation:

- Implement adapter methods in one app-owned library (single migration seam), then switch call sites.

### 2.6 Database layer and setup controller verification

Files:

- `system/mojomotor/controllers/setup.php`
- `system/mojomotor/models/*.php`

Actions:

- Re-test dbforge operations and table/key creation behavior.
- Validate query builder compatibility in high-traffic model methods.

### 2.7 Gate checks

Run full gates:

```bash
composer lint:style
composer lint:compat
composer verify:encrypt
composer audit:dynamic-properties
composer test:regression
bash scripts/smoke-matrix.sh
```

Checkpoint tag:

- `ci3-upgrade-stable`

## Phase 3: Hardening Before Release

1. Test install path from scratch (`/setup`) against empty DB.
2. Test admin flows end-to-end:
   - login/logout
   - create/edit page
   - create/edit layout
   - asset upload
   - member password reset
3. Test public flows:
   - homepage
   - page rendering
   - contact form path
   - cookie-consent behavior
4. Confirm no new fatal/deprecation errors in logs under PHP 8.1/8.2/8.3.

Release tag:

- `ci3-production-candidate`

## Rollback Plan

- Keep old deployment artifact and DB backup before release.
- If any severity-1 regression appears:
  - revert deploy to pre-upgrade artifact
  - restore pre-upgrade DB snapshot
  - rotate session and remember-me cookies if encryption/session format changed

## CI4 Future Track (Optional, Separate)

Treat CI4 as a new project, not an in-place patch.

Suggested strategy:

1. Freeze CI3 as stable legacy lane.
2. Build CI4 shell app in parallel.
3. Migrate one module at a time behind route boundaries.
4. Keep shared DB contracts stable while replacing controllers/services.

## Definition of Done (CI3 Track)

- All composer checks and smoke/regression scripts pass.
- Installer works on clean environment.
- Admin and public critical paths function without behavior regressions.
- Encryption/session migration behavior validated for existing users.
- Upgrade + rollback runbook completed and tested once in staging.

## Suggested First PR Sequence

1. PR1: Baseline artifacts + upgrade branch scaffolding + tags.
2. PR2: CI 2.2.6 bridge + bootstrap reconciliation.
3. PR3: CI3 framework swap + config merge.
4. PR4: Encrypt/session compatibility adapter and call-site migration.
5. PR5: Final bugfixes from regression/smoke and release notes.
