# Changelog

## 2026-08-15

### Added
- Initial Laravel 13 rebuild scaffold under `laravel/`.
- Laravel bootstrap files, Artisan entry point, public entry point, web and console routes.
- Smoke feature test for the home route.
- GitHub Actions workflow for Composer validation and Laravel tests.
- `.env.example` with file-based session/cache defaults for early development.
- Tracked Laravel runtime directories under `storage/framework/`.

### Preserved
- Legacy application under `../Entry Book/` remains untouched.
- Legacy XML files remain unchanged and will be treated as import/reference data only.

### Notes
- Samsung/Termux test confirmed Composer installation and Laravel package discovery work.
- Browser boot returned HTTP 500 before runtime defaults/directories were committed; the runtime fix is now part of the rebuild branch.
