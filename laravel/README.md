# OB Entry Book — Laravel rebuild

This directory contains the Laravel 13 rebuild of the Digital Security Occurrence Book.

## Legacy preservation

The original application under `../Entry Book/` remains untouched, including its PHP, JavaScript, CSS and XML data files. Laravel reads the XML only when explicitly running the legacy importer; it never writes back to the legacy files.

## Current features

- SQLite development database with migrations.
- OB entry creation with monthly `number\\month\\year` numbering.
- Monthly sequence table for safer concurrent OB allocation.
- OB history search by number, customer or entry text.
- Two-column control-room dashboard: OB entries left, management instructions right.
- Management authentication with hashed passwords.
- Controller acknowledgement using hashed PINs.
- Timestamped Laravel acknowledgements; imported legacy ACKs may retain an unknown timestamp.
- Read-only, idempotent XML importer for operators, OB entries and management instructions.
- Historical duplicate OB numbers are preserved during import.
- GitHub Actions test suite.

## Requirements

- PHP 8.3+
- Composer
- PHP SQLite and SimpleXML extensions

## First setup

From the repository root:

```bash
cd laravel
composer install
composer setup
```

`composer setup` creates `.env` from `.env.example`, generates the application key, creates `database/database.sqlite`, and runs the migrations.

Run the tests:

```bash
php artisan test
```

Start the local server:

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000`.

## Import legacy XML

Test the import without keeping database changes:

```bash
php artisan ob:import-legacy --dry-run
```

Import the preserved legacy XML:

```bash
php artisan ob:import-legacy
```

The importer defaults to `../Entry Book/data`. A different source directory can be supplied with `--path=` for testing or migration work.

## Create users

Create a manager interactively so the password does not need to appear in shell history:

```bash
php artisan ob:create-user "Operations Manager" manager
```

Create a controller interactively:

```bash
php artisan ob:create-user "Night Controller" controller
```

Management users authenticate with username/password. Controllers acknowledge management instructions with their PIN.

## Migration rule

Do not modify or delete files under `../Entry Book/` as part of the Laravel migration. Legacy data is imported into the Laravel database and retained as the historical source/reference.
