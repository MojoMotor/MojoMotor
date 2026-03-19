# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MojoMotor is a legacy PHP CMS originally by Ellis Labs, now open-sourced under GPL-3.0 and actively modernized for PHP 8.2+. The focus is compatibility and bug fixes, not new features.

## Development Setup

```bash
composer install          # Install dev tools (phpcs, phpcompatibility)
php -S 127.0.0.1:8080 -t .  # Start dev server
# Then visit http://127.0.0.1:8080/setup to run the installer
```

Before running, configure:
- [system/mojomotor/config/database.php](system/mojomotor/config/database.php) — DB credentials
- [system/mojomotor/config/config.php](system/mojomotor/config/config.php) — `base_url` and `encryption_key`

## Commands

```bash
composer lint:style              # PHP_CodeSniffer (legacy CI style, not PSR)
composer lint:compat             # PHP 8.2+ compatibility check
composer verify:encrypt          # Verify mcrypt→OpenSSL migration
composer audit:dynamic-properties # PHP 8.2 dynamic property audit
composer test:regression         # Regression workflow validation
bash scripts/smoke-matrix.sh     # Multi-PHP version smoke tests (8.1/8.2/8.3)
```

## Architecture

Built on **CodeIgniter 2.0.1** (vendored in `system/codeigniter/`). The app lives entirely in `system/mojomotor/`.

**Request flow:** `index.php` → CI bootstrap → `system/mojomotor/config/routes.php` → controller → model → view

**MVC layout:**
- `system/mojomotor/controllers/` — public controllers (`page.php`, `welcome.php`, `setup.php`) plus `admin/` subdirectory for all admin controllers
- `system/mojomotor/models/` — data access (page, layout, member, site, setup, upload)
- `system/mojomotor/views/` — templates organized by feature
- `system/mojomotor/core/` — CI framework extensions (`Mojomotor_Controller`, `Mojomotor_Config`, `Mojomotor_Security`)
- `system/mojomotor/libraries/` — custom libraries including `Auth.php` (authentication) and `Cp.php` (control panel)

**Key conventions:**
- All controllers extend `Mojomotor_Controller` (not CI_Controller directly)
- `Mojomotor_Config::site_url()` auto-detects admin vs public context
- Code style: legacy CodeIgniter (tabs, snake_case, not PSR) — enforced by `phpcs.xml.dist`
- No runtime Composer dependencies; dev tools only

**PHP 8.2+ compatibility notes:**
- Dynamic property bridge applied to CI base classes — use explicit `public $prop;` declarations when adding properties to controllers/models
- Encryption: OpenSSL only (mcrypt removed); session tokens and remember-me cookies auto-migrate on first use
- DB driver: `mysqli` (legacy `mysql` extension removed in PHP 7)

## Key Config Values

In [system/mojomotor/config/config.php](system/mojomotor/config/config.php):
- `$config['install_lock']` — set to `TRUE` after setup completes; set to `FALSE` to re-run installer
- `$config['base_url']` — must match actual server URL
- `$config['time_to_cache']` — page cache duration in minutes (0 = disabled)
