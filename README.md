# MojoMotor

The Publishing Engine that Does Less!

## Project Status:

Howdy, and thanks for looking at a fascinating bit of internet history! MojoMotor was a fun, quirky little CMS from about 2010 -- Recently (2020) I emailed with Rick Ellis formerly of Ellis Labs, and as MojoMotor was long since abandoned, he offered to transfer all rights over and signed off on relicensing it to GPL.

So, that's what I'm working on right now. Getting the codebase tidied to relicense, tested with varying versions of PHP (it doesn't currently function under stable, supported PHP releases) and -- if nothing else -- serving as a public, free archive of a neat piece of internet history.

Thanks for stopping by, any input is of course welcome!

## Modernization Docs

- Status + completed modernization tasks: [todo.md](todo.md)
- Deployment/rollback checklist: [release_checklist.md](release_checklist.md)

## Quick Local Testing

### 1) Start MojoMotor with PHP built-in server

From the repository root:

```bash
php -S 127.0.0.1:8080 -t .
```

Then open:

- `http://127.0.0.1:8080/setup` (installer)
- `http://127.0.0.1:8080/` (site)

### 2) SQLite test setup (fast local testing)

MojoMotor currently includes the legacy CodeIgniter `sqlite` driver. Before choosing SQLite in setup, verify extension support:

```bash
php -m | grep -E '(^sqlite$|^pdo_sqlite$)'
```

If the output includes `sqlite`, you can test with SQLite directly.

Suggested local DB path (example):

```bash
mkdir -p ./tmp && touch ./tmp/mojomotor.sqlite
```

In setup, use SQLite and point to that file path if prompted.

### Important SQLite note

If your PHP build only has `pdo_sqlite` (common on modern macOS/Homebrew PHP) and does **not** include the legacy `sqlite` extension, SQLite setup may fail in this legacy codebase.

For immediate testing in that case, use `mysqli` in setup, then run the included checks:

```bash
composer lint:style
composer lint:compat
composer test:regression
```
