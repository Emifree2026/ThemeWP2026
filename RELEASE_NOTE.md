# Release Note

## Fix legal page rendering and routing

- Fixed legal route template mapping in `functions.php` so `/privacy/`, `/terms/`, and `/impressum/` resolve to the correct theme templates.
- Corrected the footer legal links in `inc/footer.php` to use working English paths (`/privacy`, `/terms`, `/impressum`).
- Added `inc/markdown.php` for lightweight Markdown rendering of legal page sources.
- Fixed `inc/legal.php` parse errors and restored Markdown source rendering for legal pages.
- Updated language persistence and footer link behavior in `assets/js/sections/header.js`.
- Added local test scripts `tests/legal-md-test.php` and `tests/md-run.php` for checking Markdown rendering.

## Commit

- `aa3db7f`

## Push

- Pushed to `origin/main`
