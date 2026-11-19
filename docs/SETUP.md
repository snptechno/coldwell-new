# Coldwell Banker India: CodeIgniter 4 setup

Migration of www.coldwellbanker.in from CodeIgniter 3 to **CodeIgniter 4.7 on PHP 8.3**.

## Requirements

| | Version / notes |
|---|---|
| PHP | 8.3 (8.1+ works for CI4, but 8.3 is the target) |
| PHP extensions | **intl** (required by CI4), mbstring, json, mysqlnd/mysqli, curl, gd, fileinfo, openssl. **zip** is recommended: PhpSpreadsheet *writes* `.xls`/`.xlsx` without it, but needs it to *read* `.xlsx`. |
| Database | MySQL 8.0 (live). Local staging uses MySQL 8.0.46 on port 3307. |
| Composer | 2.x |

## Install

```bash
git clone https://github.com/snptechno/coldwell-new.git coldwell-ci4
cd coldwell-ci4
composer install
cp env .env              # then edit .env (see below)
php spark key:generate   # writes encryption.key into .env
```

If `ext-zip` cannot be enabled on a dev machine, use
`composer install --ignore-platform-req=ext-zip`. Production should have zip enabled.

## .env (never committed)

Every environment has its own `.env`. These are the keys used:

| Key | Purpose |
|---|---|
| `CI_ENVIRONMENT` | `development` locally, **`production` on live** (production hides error details) |
| `app.baseURL` | site root URL with a trailing slash |
| `app.appTimezone` | `Asia/Kolkata` |
| `database.default.*` | main DB (`coldwell`) |
| `database.tests.*` | **separate** test DB (`coldwell_tests`, prefix `tests_`), so tests never touch real data |
| `session.cookieName` | `cw4_session` (avoids clashing with the old CI3 `ci_session` cookie) |
| `security.*` | CSRF settings (session-based tokens) |
| `email.*` | SMTP. Credentials live **only** here, never in PHP code. |
| `razorpay.keyId` | Razorpay key for /quick-pay. Use a `rzp_test_...` key on staging and the live key only on live. If it is missing, the Pay button cannot open. |
| `google.mapsKey` | Google Maps JavaScript API key for the /our-franchise map. Restrict it to the site's domains in Google Cloud. |

## Web server

- **Live / staging server:** point the Apache `DocumentRoot` at `public/`. `app/`, `vendor/`, `writable/` and `.env` must sit outside the web root.
- **Local XAMPP (inside `htdocs`):** the root `.htaccess` maps every request into `public/`, so the app is served at `http://localhost/coldwell-ci4/` and nothing outside `public/` is reachable.
- `writable/` (cache, logs, session, uploads) must be writable by the web server user.

## Built-in security baseline

- **Auto-routing is off.** Every URL is declared in `app/Config/Routes.php` together with the HTTP methods it accepts.
- **Global filters:** `csrf` and `invalidchars` run before every request, and `secureheaders` runs after every response.
- **`adminauth` filter:** protects the whole `admin` route group, replacing the per-controller login checks.
- **Dev error output never includes secrets:**
  - `App\Debug\SafeExceptionHandler` strips trace argument values from JSON errors.
  - The HTML exception view prints objects and arrays by type only.
  - Secret-looking keys are masked.
  - The toolbar does not collect view data.
- **Output is escaped with `esc()`** in all views.

## Useful commands

```bash
php spark routes                 # list routes with their filters
php spark filter:check GET /     # filters applied to a route (Git Bash: prefix MSYS_NO_PATHCONV=1)
php vendor/bin/phpunit           # tests (uses the database.tests group)
composer audit                   # dependency security advisories
```
