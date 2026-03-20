# PHP smoke-check report

- Generated: 2026-03-20 00:29:30 UTC
- Endpoints: `/`, `/setup`, `/index.php/admin/login`, `/index.php/page/content`

| PHP | Pass/Total | First fatal |
|---|---:|---|
| local-php | 2/4 | None |

## Details

### PHP local-php
- PASS / (HTTP 200)
- PASS /setup (HTTP 200)
- FAIL /index.php/admin/login (HTTP 500)
  - Snippet: <html> <head> <title>Database Error</title> <style type="text/css"> body { background-color: #fff; margin: 40px; font-family: Lucida Grande, Verdana, Sans-serif; font-size: 12px; color: #000; } #content { border: #999 1p
- FAIL /index.php/page/content (HTTP 500)
  - Snippet: <html> <head> <title>Database Error</title> <style type="text/css"> body { background-color: #fff; margin: 40px; font-family: Lucida Grande, Verdana, Sans-serif; font-size: 12px; color: #000; } #content { border: #999 1p

