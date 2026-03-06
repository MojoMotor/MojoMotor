# Regression workflow report

- Generated: 2026-03-06 03:24:02 UTC
- Base URL: http://127.0.0.1:8080
- Result: 6/6

| Check | Method | Path | Status | Result |
|---|---|---|---:|---|
| homepage_get | GET | / | 200 | PASS |
| setup_get | GET | /setup | 200 | PASS |
| admin_login_get | GET | /index.php/admin/login | 200 | PASS |
| page_render_get | GET | /index.php/page/content | 200 | PASS |
| admin_login_post_invalid | POST | /index.php/admin/login/process | 500 | PASS |
| admin_pages_update_post_unauth | POST | /index.php/admin/pages/update | 500 | PASS |

## Failures

- None
