# Security audit report (OWASP Top 10 focused)

- Generated: 2026-03-06 UTC
- Scope: `system/mojomotor/**`, `system/codeigniter/system/**`, runtime/config defaults in repository
- Method: static code/config review with targeted pattern analysis and manual triage

## Executive summary

This codebase has **multiple high-risk legacy security issues** that are common in pre-modern PHP CMS stacks.
Most notable are weak credential/session primitives, hard-coded crypto material, broad upload permissions, and lack of modern cookie/header hardening.

The most urgent work is:
1) migrate password/session/remember-me design,
2) rotate and externalize secrets,
3) lock down uploads and serialization paths,
4) add modern defense-in-depth headers/cookie attributes.

## Findings mapped to OWASP

### F-01 (High) — Weak password hashing (`A02:2021-Cryptographic Failures`, `A07:2021-Identification and Authentication Failures`)
- Evidence:
  - `system/mojomotor/models/member_model.php` (`generate_password()` uses `do_hash`)
  - `system/codeigniter/system/helpers/security_helper.php` (`do_hash()` defaults to `sha1`, fallback `md5`)
- Risk:
  - SHA-1/MD5 are not suitable for password storage; offline cracking cost is too low.
- Recommendation:
  - Migrate to `password_hash()` / `password_verify()` (`PASSWORD_ARGON2ID` preferred, fallback `PASSWORD_BCRYPT`).
  - Implement phased rehash-on-login migration for existing users.

### F-02 (High) — Hard-coded encryption key in repository (`A02:2021-Cryptographic Failures`, `A05:2021-Security Misconfiguration`)
- Evidence:
  - `system/mojomotor/config/config.php` sets `$config['encryption_key'] = 'mojo_S5KXAB8K';`
- Risk:
  - Any party with source access can forge/decrypt framework-encrypted values depending on environment parity.
- Recommendation:
  - Move key to environment/secret manager, rotate immediately, and invalidate affected persistent auth/session artifacts.

### F-03 (High) — Remember-me design does not verify server-side token (`A07:2021-Identification and Authentication Failures`)
- Evidence:
  - `system/mojomotor/libraries/Auth.php` `_set_remember_me()` generates token but stores encrypted blob as-is.
  - `_check_remember_me()` decodes cookie and trusts `user_id` + timeout; it does **not** compare parsed token to a server-side token value.
- Risk:
  - Persistent auth integrity relies too heavily on cookie secrecy; design is brittle and hard to revoke per-device/session.
- Recommendation:
  - Store hashed random token server-side (selector/validator pattern), compare on each use, rotate token on every login/reuse.

### F-04 (Medium/High) — Session fixation hardening gap (`A07:2021-Identification and Authentication Failures`)
- Evidence:
  - `system/mojomotor/libraries/Auth.php` successful login path sets session data but does not regenerate session ID.
- Risk:
  - Increased session fixation risk on authentication boundary.
- Recommendation:
  - Regenerate session identifier immediately after authentication and privilege changes.

### F-05 (Medium/High) — Cookies lack modern hardening attributes (`A05:2021-Security Misconfiguration`)
- Evidence:
  - `system/codeigniter/system/core/Input.php` and custom cookie/session/security set cookies via legacy `setcookie(...)` signature without HttpOnly/SameSite.
  - `cookie_secure` is referenced but not explicitly hardened in app config defaults.
- Risk:
  - Elevated exposure to cookie theft/misuse and cross-site request scenarios.
- Recommendation:
  - Enforce `Secure`, `HttpOnly`, and `SameSite=Lax/Strict` on all auth/session cookies.
  - Prefer HTTPS-only deployments with strict redirect/HSTS policy.

### F-06 (Medium) — Unsafe deserialization on user-controlled POST value (`A03:2021-Injection`, `A08:2021-Software and Data Integrity Failures`)
- Evidence:
  - `system/mojomotor/controllers/admin/pages.php` unserializes posted `parent_hierarchy`.
- Risk:
  - `unserialize()` on untrusted input can enable object injection and logic abuse.
- Recommendation:
  - Replace with JSON (`json_decode(..., true)`) and strict schema validation.

### F-07 (High) — Default upload policy allows all file types (`A05:2021-Security Misconfiguration`, `A03:2021-Injection`)
- Evidence:
  - `system/mojomotor/controllers/setup.php` seeds `upload_prefs.allowed_types = 'all'`.
  - `system/mojomotor/controllers/admin/editor.php` maps `'all'` to upload `allowed_types='*'`.
- Risk:
  - Increases probability of dangerous file upload chains (especially if upload path is web-executable or misconfigured).
- Recommendation:
  - Restrict to explicit allowlist, separate storage outside web root, disable script execution in upload dirs, enforce MIME/content validation.

### F-08 (Medium) — No visible login throttling / lockout (`A07:2021-Identification and Authentication Failures`)
- Evidence:
  - `system/mojomotor/controllers/admin/login.php` handles auth failures but no rate-limit, delay, lockout, or CAPTCHA controls are present.
- Risk:
  - Brute-force credential stuffing resistance is weak.
- Recommendation:
  - Add per-IP and per-account rate limits, progressive backoff, and alerting.

### F-09 (Medium) — Legacy third-party JS/editor stack (`A06:2021-Vulnerable and Outdated Components`)
- Evidence:
  - `system/mojomotor/javascript/ckeditor/ckeditor.js` reports `version:'3.6.3'`.
  - Legacy bundled JS stack indicates substantial age.
- Risk:
  - Historical client-side vulnerabilities likely apply; patch support may be unavailable.
- Recommendation:
  - Upgrade/replace editor and frontend dependencies to currently maintained versions.

### F-10 (Medium) — Missing explicit app-level security headers (`A05:2021-Security Misconfiguration`)
- Evidence:
  - No explicit CSP/HSTS/X-Frame-Options/X-Content-Type-Options/Referrer-Policy controls found in application header handling paths reviewed.
- Risk:
  - Reduced browser-side mitigations for XSS/clickjacking/MIME confusion.
- Recommendation:
  - Add baseline header policy at web server or framework middleware layer.

### F-11 (Low/Medium) — Sensitive setup/install and configuration mutation surface (`A05:2021-Security Misconfiguration`)
- Evidence:
  - Install/update and config mutation flows in `system/mojomotor/controllers/setup.php` and `system/mojomotor/core/Mojomotor_Config.php` are powerful and file-writing.
- Risk:
  - Operational mistakes (e.g., installer exposure, weak file permissions) could become high impact.
- Recommendation:
  - Enforce one-way install lock in production, remove setup routes in deployed builds, harden file permissions.

### F-12 (Low/Medium) — Logging/monitoring maturity gap (`A09:2021-Security Logging and Monitoring Failures`)
- Evidence:
  - `system/mojomotor/config/config.php` uses low/default-style logging and no clear auth-event telemetry in reviewed paths.
- Risk:
  - Slower detection/response for credential abuse and admin actions.
- Recommendation:
  - Add structured security event logging for login failures, password resets, privilege changes, and admin actions.

## Positive controls observed

- CSRF protection is enabled in config (`csrf_protection = TRUE`).
- Role checks (`is_admin` / `is_editor`) are used broadly across admin controllers.
- Some input validation is present via form validation rules.
- File/path handling includes `sanitize_filename()` in some asset-loading paths.

## Prioritized remediation plan

### Phase 0 (Immediate, 24-72h)
1. Rotate and externalize `encryption_key`.
2. Restrict uploads to safe allowlist and prevent script execution in upload storage.
3. Add login throttling and session ID regeneration on auth.
4. Force secure cookie policy (`Secure`, `HttpOnly`, `SameSite`) and HTTPS.

### Phase 1 (1-2 sprints)
1. Password hash migration to Argon2id/Bcrypt with rolling rehash.
2. Rebuild remember-me to server-validated token model.
3. Remove all `unserialize()` on request data (switch to JSON).
4. Add baseline security headers and centralize header policy.

### Phase 2 (Strategic)
1. Upgrade legacy framework/components (including CKEditor).
2. Introduce centralized authn/authz middleware and policy checks.
3. Add automated SAST/dependency checks and security regression tests.
4. Create security hardening guide for deployment defaults.

## Architectural critique (for roadmap discussion)

- The stack is tightly coupled to a legacy CI2-era architecture with custom overrides for session/security behavior, increasing upgrade friction and security debt.
- Security controls are dispersed (helpers/core/controllers) rather than centralized, which makes assurance and auditing harder.
- Configuration and installer logic includes runtime file mutation patterns that are operationally risky in modern immutable/deployment-pipeline environments.
- CMS features intentionally allow rich content and addons; without modern sandboxing and boundary controls, impact radius of any compromise is high.

## Confidence and limits

- Confidence: moderate-high for findings above based on direct code/config evidence.
- This was a static review only; no dynamic exploitation, no full dependency CVE enumeration, and no infrastructure/web-server config validation were performed.
- Actual production risk depends on deployment controls (WAF, reverse proxy headers, filesystem permissions, TLS posture, admin exposure).