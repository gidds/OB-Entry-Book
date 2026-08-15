# TODO

## Current
- [ ] Re-test fresh Laravel browser boot after runtime directory and `.env.example` fixes.
- [ ] Add SQLite database file/bootstrap handling for development and tests.
- [ ] Add `OccurrenceEntry` migration and model.
- [ ] Add server-side monthly OB number generation using `number\\month\\year` format.
- [ ] Add occurrence entry create/store flow and feature tests.

## Next
- [ ] Add management users/controllers and authentication.
- [ ] Add management instructions migration/model/create flow.
- [ ] Add controller PIN acknowledgement with hashed PIN storage.
- [ ] Build the two-column control-room dashboard: OB entries left, management instructions right.
- [ ] Add occurrence search/history.
- [ ] Add one-shot idempotent XML importer for entries, instructions, acknowledgements and operators.
- [ ] Preserve legacy IDs during XML import for traceability.
- [ ] Add tests proving XML import never modifies legacy XML files.

## Later
- [ ] Review legacy delivery, collection, theft, incident and after-hours workflows for dedicated entry types/fields.
- [ ] Add reporting/export features after core migration is stable.
- [ ] Improve responsive tablet/desktop control-room UI.

## Migration rule
- Never modify or delete files under `../Entry Book/` during the Laravel migration.
