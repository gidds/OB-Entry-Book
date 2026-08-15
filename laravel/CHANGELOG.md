# Changelog

## 2026-08-15

### Added
- Initial Laravel 13 rebuild scaffold under `laravel/`.
- Laravel bootstrap, runtime configuration, Artisan entry point and GitHub Actions testing.
- One-command local setup via `composer setup`.
- SQLite migrations and `OccurrenceEntry` model.
- Monthly OB numbering in the legacy `number\\month\\year` format.
- Monthly OB sequence table to reduce concurrent-number collisions while allowing duplicate historical imports.
- OB entry form, history list and search by OB number, customer or entry text.
- Responsive two-column control-room dashboard with OB entries left and management instructions right.
- User roles for controller, manager and admin.
- Management login/logout with hashed passwords.
- Management instruction creation.
- Controller acknowledgement with hashed PINs and acknowledgement records.
- `ob:create-user` command for secure user provisioning.
- Read-only `ob:import-legacy` command with optional `--dry-run` and `--path`.
- Idempotent XML import for operators, occurrence entries, instructions and existing acknowledgements.
- Legacy migration tests that verify source XML hashes do not change.
- Tests for OB numbering, search, authentication, management instructions, PIN acknowledgement, user provisioning and XML migration.

### Changed
- Explicit app, database, auth, session, cache, logging and hashing configuration replaces reliance on framework fallbacks.
- CI now creates `.env` from `.env.example`, eliminating the earlier PHPUnit missing-`.env` warnings.
- Historical OB numbers are indexed rather than globally unique because the legacy XML contains real duplicate OB numbers/IDs.
- Imported legacy acknowledgement timestamps may be `null` because the old XML records who acknowledged an instruction but not necessarily when.

### Preserved
- The complete legacy application under `../Entry Book/` remains untouched.
- `entries.xml`, `instructions.xml` and `auth.xml` remain read-only historical/import sources.
- Legacy duplicate entries are preserved rather than discarded to satisfy a new database constraint.

### Testing notes
- Samsung/Termux confirmed Composer installation and Laravel package discovery.
- The first phone browser test returned HTTP 500 while the hand-built scaffold still lacked runtime environment setup.
- GitHub Actions later identified the test warning as a missing `.env`; environment/runtime defaults have since been committed.
- Occurrence-entry tests passed after correcting the SQLite date assertion.
- The real legacy XML migration safety suite passed, including idempotency, source-file integrity, hashed PIN verification and duplicate historical OB preservation.
