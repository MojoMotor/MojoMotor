# MojoMotor

The Publishing Engine that Does Less!

## Project Status:

Howdy, and thanks for looking at a fascinating bit of internet history! MojoMotor was a fun, quirky little CMS from about 2010 -- Recently (2020) I emailed with Rick Ellis formerly of Ellis Labs, and as MojoMotor was long since abandoned, he offered to transfer all rights over and signed off on relicensing it to GPL.

So, that's what I'm working on right now. Getting the codebase tidied to relicense, tested with varying versions of PHP (it doesn't currently function under stable, supported PHP releases) and -- if nothing else -- serving as a public, free archive of a neat piece of internet history.

Thanks for stopping by, any input is of course welcome!

## Modernization Docs

- Status + completed modernization tasks: [todo.md](todo.md)
- Deployment/rollback checklist: [release_checklist.md](release_checklist.md)
- Framework upgrade planning notes: [codeigniter-update.md](codeigniter-update.md)

## New contributor quickstart (5 minutes)

1. Clone the repo and `cd` into it.
2. Install tools: `composer install`.
3. Create an empty MySQL/MariaDB database for local setup.
4. Start the app: `php -S 127.0.0.1:8080 -t .`.
5. Visit `http://127.0.0.1:8080/setup` and complete install using `mysqli`.
6. Run baseline checks:

   ```bash
   composer lint:style
   composer lint:compat
   composer test:regression
   ```

7. (Optional) Run full modernization checks:

   ```bash
   composer verify:encrypt
   composer audit:dynamic-properties
   bash scripts/smoke-matrix.sh
   ```

### Quick troubleshooting

- `composer: command not found`: install Composer and re-run `composer install`.
- `Class "mysqli" not found` or setup DB errors: enable/install the `mysqli` extension in your PHP build.
- `Address already in use` on port `8080`: use a different port, for example `php -S 127.0.0.1:8081 -t .`.
- Setup page loops or behaves as already installed: check/remove `install_lock` in your writable app path for a fresh local install.
- Smoke matrix does not run all versions: if Docker or versioned local binaries are unavailable, `scripts/smoke-matrix.sh` falls back to local `php` and writes `reports/smoke/result-local-php.json`.

### Reporting issues

When opening an issue, include your PHP version (`php -v`), DB type/version, exact reproduction steps, and relevant generated artifacts from `reports/` (for example `reports/smoke/report.md`, `reports/regression/report.md`, plus matching `result-*.json`).

If you're filing through GitHub, use the Bug report issue template to include this information consistently.

## Local Development

### Prerequisites

- PHP 8.2+ (8.3 also validated in this repo workflows)
- A MySQL/MariaDB database for local installs (`mysqli`)
- Composer (for linting and modernization checks)

### 1) Install dev tooling

From the repository root:

```bash
composer install
```

### 2) Start MojoMotor with PHP built-in server

From the repository root:

```bash
php -S 127.0.0.1:8080 -t .
```

Then open:

- `http://127.0.0.1:8080/setup` (installer)
- `http://127.0.0.1:8080/` (site)

### 3) Run checks during development

After setup (using `mysqli`), run:

```bash
composer lint:style
composer lint:compat
composer verify:encrypt
composer audit:dynamic-properties
composer test:regression
```

For multi-version smoke checks, run:

```bash
bash scripts/smoke-matrix.sh
```

## Repository layout

- `system/mojomotor/`: MojoMotor application code (controllers, models, views, config, libraries)
- `system/codeigniter/system/`: vendored legacy CodeIgniter framework runtime
- `scripts/`: modernization and validation scripts used by local/CI workflows
- `reports/`: generated report artifacts (`report.md`, `result-*.json`) for smoke, regression, encryption, and dynamic-property checks
- `mm_uploads/`: writable uploads/content directory used by the application
- `import/`: default static import/source assets used by setup and migration paths
- `user_guide/`: legacy MojoMotor user documentation

## Generated artifacts

Most generated artifacts for checks are written under `reports/` subdirectories:

- `reports/smoke/`
- `reports/regression/`
- `reports/encryption/`
- `reports/dynamic-properties/`

When running release validation, pair these with [release_checklist.md](release_checklist.md) and archive outputs with your release notes.
