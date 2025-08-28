# Repository Guidelines

## Project Structure & Module Organization
- Core plugin entry: `takamoa-papi-integration.php` (hooks, bootstrap).
- Logic: `includes/` (loader, i18n, activator/deactivator, helpers).
- Admin UI: `admin/` (classes, `js/`, `css/`, `partials/`).
- Public UI: `public/` (Vue/JS form, styles).
- Translations: `languages/` (`.pot` and future locales).
- Uninstall routine: `uninstall.php`.

## Build, Test, and Development Commands
- Activate in a local site: `wp plugin activate takamoa-papi-integration`.
- Deactivate: `wp plugin deactivate takamoa-papi-integration`.
- Generate a distributable zip (from repo root):
	- `zip -r takamoa-papi-integration.zip . -x '*.git*'`.
- Flush rewrite rules after adding endpoints: `wp rewrite flush`.
- Optional i18n POT refresh (if wp-cli i18n is available): `wp i18n make-pot . languages/takamoa-papi-integration.pot`.

## Coding Style & Naming Conventions
- Follow WordPress PHP standards: tabs for indentation, spaces for alignment.
- Prefix everything with `takamoa_papi_integration` (functions, options, nonces).
- File names: `class-takamoa-papi-integration-*.php`; Classes: `Takamoa_Papi_Integration_*`.
- Escape output (`esc_html`, `esc_attr`, `wp_kses_post`), sanitize input (`sanitize_text_field`, `sanitize_email`).
- Use nonces and capability checks for all admin/AJAX actions.
- Text domain: `Takamoa_Papi_Integration` (keep strings translatable).

## Testing Guidelines
- No automated tests yet; add unit tests with PHPUnit when introducing complex logic.
- Manual checks before PR:
	- Shortcode `[takamoa_papi_form]` renders and submits.
	- Payment link creation, polling, and status transitions.
	- CSV export, ticket generation, and scanner validation.
	- i18n strings appear and load from `languages/`.

## Commit & Pull Request Guidelines
- Use Conventional Commits where possible: `feat:`, `fix:`, `chore:`, `refactor:`, `style:`.
- Branch names: `feat/…`, `fix/…`, `chore/…`.
- PRs must include: clear description, linked issues, screenshots/GIFs for UI, test steps, and impact notes (DB/options, endpoints, nonces).
- Keep diffs focused; document any schema or option key changes in README.

## Security & Configuration Tips
- Never commit API keys; store the Papi API key via plugin settings.
- Validate and sanitize all user input; escape all output.
- Rate-limit/nonce-protect AJAX and verify capabilities in admin.
- On activation, ensure tables/options exist; on uninstall, cleanly remove options only.
