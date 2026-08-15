# TODO

## Current
- [ ] Re-test a fresh clone/browser boot on the PC using `composer install` then `composer setup`.
- [ ] Confirm the latest full GitHub Actions run is green after sequence/search/provisioning changes.
- [ ] Review and remove the temporary `laravel-rebuild-runtime-temp` branch created during connector testing.
- [ ] Add a small admin/user-management web interface if command-line provisioning proves inconvenient.

## Completed core migration
- [x] Add SQLite database/bootstrap handling for development and tests.
- [x] Add `OccurrenceEntry` migration and model.
- [x] Add server-side monthly OB number generation using `number\\month\\year` format.
- [x] Add safer monthly sequence allocation for new OB entries.
- [x] Add occurrence entry create/store flow and feature tests.
- [x] Add management users/controllers and authentication.
- [x] Add management instructions migration/model/create flow.
- [x] Add controller PIN acknowledgement with hashed PIN storage.
- [x] Build the two-column control-room dashboard: OB entries left, management instructions right.
- [x] Add occurrence search/history.
- [x] Add idempotent XML importer for entries, instructions, acknowledgements and operators.
- [x] Preserve legacy identifiers/keys for traceability.
- [x] Preserve duplicate historical OB numbers found in legacy XML.
- [x] Add tests proving XML import never modifies legacy XML files.
- [x] Add `--dry-run` legacy import mode.
- [x] Add secure CLI provisioning for controllers/managers/admins.
- [x] Add CI environment preparation so tests do not warn about missing `.env`.

## Next functional work
- [ ] Review the old search page in detail for date-range/filter behavior worth carrying forward.
- [ ] Add pagination rather than the temporary 100-entry dashboard limit.
- [ ] Add management-instruction history/filtering and optional archived state.
- [ ] Decide whether every controller must ACK each instruction or whether a single controller ACK remains the operational rule.
- [ ] Add audit/event logging for logins, OB creation, instruction creation and acknowledgements.
- [ ] Add controller/manager active/inactive status instead of deleting users.
- [ ] Add password/PIN rotation flows.
- [ ] Decide backup/restore process for the production database.

## Later
- [ ] Review legacy delivery, collection, theft, incident and after-hours workflows for dedicated entry types/fields.
- [ ] Add reporting/export features after core migration is stable.
- [ ] Improve responsive tablet/desktop control-room UI after real operator testing.
- [ ] Add role/permission granularity if management and admin responsibilities diverge.
- [ ] Consider PostgreSQL/MariaDB for multi-station production deployment instead of SQLite.

## Migration rule
- Never modify or delete files under `../Entry Book/` during the Laravel migration.
