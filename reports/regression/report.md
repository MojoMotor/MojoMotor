# Regression workflow report

- Generated: 2026-03-20 00:28:13 UTC
- Base URL: http://127.0.0.1:8080
- Result: 4/6

| Check | Method | Path | Status | Result |
|---|---|---|---:|---|
| homepage_get | GET | / | 200 | PASS |
| setup_get | GET | /setup | 200 | PASS |
| admin_login_get | GET | /index.php/admin/login | 500 | FAIL |
| page_render_get | GET | /index.php/page/content | 500 | FAIL |
| admin_login_post_invalid | POST | /index.php/admin/login/process | 500 | PASS |
| admin_pages_update_post_unauth | POST | /index.php/admin/pages/update | 500 | PASS |

## Failures

- admin_login_get (/index.php/admin/login, HTTP 500)
  - Snippet: <html> <head> <title>Database Error</title> <style type="text/css"> body { background-color: #fff; margin: 40px; font-family: Lucida Grande, Verdana, Sans-serif; font-size: 12px; color: #000; } #content { border: #999 1p
- page_render_get (/index.php/page/content, HTTP 500)
  - Snippet: <html> <head> <title>Database Error</title> <style type="text/css"> body { background-color: #fff; margin: 40px; font-family: Lucida Grande, Verdana, Sans-serif; font-size: 12px; color: #000; } #content { border: #999 1p
