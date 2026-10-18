# Security Policy

## Reporting a vulnerability

If you find a security issue, please **do not open a public issue**.
Report it privately through GitHub's **"Report a vulnerability"** button (Security tab → Advisories),
including steps to reproduce. You will receive a reply as soon as possible.

## What this repository does and does not contain

This is a portfolio copy of the source code. It contains **no**:

- `.env` files, application keys, passwords or API keys
- databases, SQL dumps, logs, sessions or uploaded files
- customer or order data
- production hostnames or server configuration

Anyone running the project generates their own `APP_KEY`, and the owner account is created only from
values the operator places in their own `.env` (`SUPER_ADMIN_EMAIL`, `SUPER_ADMIN_PASSWORD`).

## Hardening checklist for a production deployment

1. `APP_ENV=production`, `APP_DEBUG=false`
2. Serve over HTTPS and set `SESSION_SECURE_COOKIE=true`
3. Change `ADMIN_PATH` to a private, non-guessable value
4. Keep `OTP_DEBUG_SHOW=false` (it is ignored outside `local` anyway)
5. Point the web root at `public/` only
6. Use a strong owner password and remove `SUPER_ADMIN_PASSWORD` from `.env` after seeding
7. Run `php artisan config:cache route:cache view:cache` and keep dependencies updated
