# Alte Ansichten

A Laravel 11 application for historical municipalities, places, images, stories, QR codes, moderated user submissions, and public pages.

## Run & Operate

- `cd alte-ansichten && php artisan serve --host=0.0.0.0 --port=8000` — run the Laravel dev server (port 8000)
- `cd alte-ansichten && php artisan --version` — check Laravel version
- `cd alte-ansichten && composer install` — install PHP dependencies
- `cd alte-ansichten && php artisan test` — run the test suite
- `cd alte-ansichten && php artisan migrate` — run database migrations

## Stack

- PHP 8.4, Laravel 11
- SQLite (dev default) → MySQL/PostgreSQL for production
- Filament 3 (not yet installed)
- Composer for PHP package management

## Where things live

- `alte-ansichten/` — the Laravel 11 project root (standard structure)
  - `app/` — application code (Models, Controllers, etc.)
  - `bootstrap/` — framework bootstrap files
  - `config/` — configuration files
  - `database/` — migrations, seeders, factories
  - `public/` — web root (index.php, assets)
  - `resources/` — views, CSS, JS
  - `routes/` — web.php, api.php, etc.
  - `storage/` — logs, cache, uploaded files
  - `tests/` — PHPUnit tests
  - `artisan` — Laravel CLI entry point
  - `composer.json` — PHP dependencies
  - `.env` — local environment config (not committed)
  - `.env.example` — environment template

## Architecture decisions

- Laravel project lives in `alte-ansichten/` subdirectory (not workspace root) to avoid conflicting with the existing Node.js monorepo scaffolding in the workspace root.
- SQLite used as default database for local development (zero-config). Will switch to MySQL/PostgreSQL on the production VM.
- No Replit-specific dependencies added — the project is fully portable and deployable on a standard Linux VM.
- Deployment path: Replit development → GitHub → own VM at spaceship.com.

## Product

Historical image and place archive called "Alte Ansichten". Will include municipalities, places, historical images, stories, QR codes, moderated user submissions, and public-facing pages.

## User preferences

- No Replit-specific dependencies — project must be portable to a standard Linux VM.
- Deployment workflow: Replit → GitHub → own VM.
- Tasks are done one step at a time; do not continue to next task without being asked.

## Gotchas

- Always `cd alte-ansichten` before running `php artisan` commands.
- The workspace root contains a Node.js pnpm monorepo — do not mix Laravel files into the root.
- `.env` is not committed to git; `.env.example` is the template.
- When deploying to production VM, switch `DB_CONNECTION` from `sqlite` to `mysql` or `pgsql` and set the appropriate credentials.
- `APP_URL` / `ASSET_URL` must NOT include `:8000` on Replit. The Replit proxy sends `X-Forwarded-Host` without a port; signed URLs must match the port-free public hostname. Wrong: `https://….replit.dev:8000`. Correct: `https://….replit.dev`.
- `livewire/upload-file` is excluded from CSRF verification in `bootstrap/app.php` (it is secured by signed URL instead). This is required because the Replit iframe preview blocks session cookies on cross-origin XHRs, causing 419 errors.

## Pointers

- See the `pnpm-workspace` skill for workspace structure, TypeScript setup, and package details (Node.js side)
- Laravel docs: https://laravel.com/docs/11.x
