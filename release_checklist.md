# MojoMotor PHP Modernization Release Checklist

## Pre-release preparation

- [ ] Confirm target runtime versions are available on host(s): PHP 8.2 primary, PHP 8.3 validated.
- [ ] Confirm DB driver migration defaults are in place (`mysqli`) for fresh installs.
- [ ] Review local changes in `composer.json`, `phpcs.xml.dist`, and modernization scripts.
- [ ] Ensure `install_lock` state is correct for target environment before deployment.

## Backup and rollback prerequisites

- [ ] Take a full database backup (schema + data) and verify restore command works.
- [ ] Take a filesystem backup of the full app root, including `mm_uploads/` and config files.
- [ ] Record current deployed commit/tag and runtime versions.
- [ ] Pre-stage rollback package (previous release artifact) on host.

## Configuration verification

- [ ] Verify `system/mojomotor/config/config.php` values (base URL, index page, encryption key, cookie/session settings).
- [ ] Verify `system/mojomotor/config/database.php` values (host/user/pass/db prefix/driver).
- [ ] Verify file and directory permissions for cache/session/upload paths.

## Deploy

- [ ] Deploy code artifact.
- [ ] Keep previous artifact available for immediate rollback.
- [ ] Run dependency/tool sanity check if needed (`composer lint:style`, `composer lint:compat`).

## Post-deploy smoke checks

- [ ] Run runtime smoke checks (`scripts/smoke-matrix.sh` or equivalent host-specific execution).
- [ ] Run regression workflow checks (`composer test:regression`).
- [ ] Verify homepage, setup route behavior, login page, and a sample content page load.
- [ ] Verify admin login succeeds with valid credentials.
- [ ] Verify page update/add flow in admin UI (manual confirmation in deployed environment).

## Encryption/session migration checks

- [ ] Run encryption migration verification (`composer verify:encrypt`).
- [ ] Confirm legacy mcrypt-derived token policy in release comms: forced re-auth expected where applicable.
- [ ] Validate new remember-me/session tokens are issued after successful login.

## Known limitations to include in release notes

- Dynamic property compatibility bridge is currently applied on CI base classes.
- Additional explicit-property refactors remain for some legacy classes.
- Full containerized multi-version smoke matrix depends on Docker availability in the execution environment.

## Rollback procedure

1. Enable maintenance mode (or otherwise pause write traffic).
2. Restore previous code artifact.
3. Restore database backup if schema/content changes were applied and need reversal.
4. Clear generated/runtime cache as appropriate.
5. Re-run minimal smoke checks on rolled-back version (`/`, admin login page, one content page).
6. Disable maintenance mode.
7. Record rollback reason and attach failure evidence from smoke/regression artifacts.
