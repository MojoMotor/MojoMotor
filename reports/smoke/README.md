# Smoke matrix

Run the PHP version smoke matrix from project root:

```bash
bash scripts/smoke-matrix.sh
```

Execution modes:

- **Docker mode (preferred):** runs `php:8.1-cli`, `php:8.2-cli`, `php:8.3-cli`.
- **Local fallback mode:** if Docker is unavailable, tries `php8.1`, `php8.2`, `php8.3` binaries.
- **Single-binary fallback:** if only `php` exists, writes `result-local-php.json`.

Outputs:

- `reports/smoke/result-8.1.json`
- `reports/smoke/result-8.2.json`
- `reports/smoke/result-8.3.json`
- `reports/smoke/report.md`

In fallback mode, labels may differ (for example `result-local-php.json`).

The report captures pass/fail for:

- `/`
- `/setup`
- `/index.php/admin/login`
- `/index.php/page/content`

and includes the first fatal snippet (if detected) per PHP version.
