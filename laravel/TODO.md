# TODO

## Current
- [ ] Re-test a fresh clone/browser boot on the PC using `composer install` then `composer setup`.
- [ ] On the PC, run the real legacy import and inspect imported OB entries/instructions in the browser.
- [ ] Provision a test manager and controller with `ob:create-user` and test the complete browser workflow.
- [ ] Create a new OB entry after legacy import and verify the monthly sequence continues correctly from imported historical data.
- [ ] Test management login, instruction creation, logout and controller PIN acknowledgement through the browser.
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
- [x] Add paginated OB history/dashboard instead of a fixed 100-entry display limit.
- [x] Add idempotent XML importer for entries, instructions, acknowledgements and operators.
- [x] Preserve legacy identifiers/keys for traceability.
- [x] Preserve duplicate historical OB numbers found in legacy XML.
- [x] Add tests proving XML import never modifies legacy XML files.
- [x] Add `--dry-run` legacy import mode.
- [x] Add secure CLI provisioning for controllers/managers/admins.
- [x] Add CI environment preparation so tests do not warn about missing `.env`.
- [x] Confirm full GitHub Actions suite is green after sequence/search/provisioning changes.
- [x] Validate the full Laravel test suite on Samsung S23 Ultra/Termux: 25 tests, 76 assertions, all passing.
- [x] Validate the real legacy XML dry-run on Samsung/Termux: 2 operators, 15 OB entries and 15 instructions parsed successfully.

## Next functional work
- [ ] After approximately two weeks of operator familiarisation, add the control-room access layer rather than changing the current live workflow immediately.
- [ ] Allow trusted office public IP address(es) to open the normal controller dashboard without a login; keep trusted IPs configurable rather than hard-coded.
- [ ] Require authentication when the OB site is accessed from outside the trusted office public IP address(es).
- [ ] Use one login screen rather than separate controller and management login pages: controllers authenticate with their unique PIN credentials, while managers/admins retain username/password authentication.
- [ ] Keep management/admin functions protected by management authentication even when the request originates from the trusted office public IP.
- [ ] After operators are comfortable with the new system, require the controller's unique PIN when saving an OB entry so the posting controller is recorded automatically for traceability; do not introduce this requirement during the initial familiarisation period.
- [ ] Keep the existing controller PIN acknowledgement flow for management instructions.
- [ ] Keep the OB site excluded from search indexing with noindex/robots protection in addition to authentication.
- [ ] Add customer filter to OB history/dashboard.
- [ ] Keep the operator Customer/Site field as simple free text rather than forcing a dropdown/autocomplete workflow.
- [ ] Add customer/site alias learning: map different spellings, abbreviations and common operator variants to one canonical customer/site for filtering and reports.
- [ ] Add a manager/admin review area for unknown or ambiguous customer/site names, allowing management to confirm which canonical customer/site each variant belongs to; retain confirmed mappings for future entries.
- [ ] Add XML export for managers and administrators.
- [ ] Add manager/admin weekly report export grouped or filtered by canonical site/customer while preserving the operator's original entered text.
- [ ] Review the old search page in detail for date-range/filter behavior worth carrying forward.
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
