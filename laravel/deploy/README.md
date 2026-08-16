# InterWorx / shared-hosting demo deployment

Target layout:

```text
/home/rdtzzbgb/ais-grp.co.za/
  ob-book-app/                 # private Laravel application
  html/
    ob-book/                   # public subdomain document root
      index.php
      .htaccess
```

Production database defaults used by the browser installer:

- Host: `localhost`
- Database: `rdtzzbgb_ob_entry_book`
- User: `rdtzzbgb_ob_app`
- Password: entered privately in `/setup`

## Build on Windows

From the Laravel folder:

```powershell
.\deploy\build-shared-hosting.ps1
```

The script creates `build/ob-entry-book-demo.zip` containing production Composer dependencies and a unique application key. No Composer or Artisan access is required on the hosting account.

## Upload

Upload the ZIP to `/home/rdtzzbgb/ais-grp.co.za/` and extract it there. Ensure the Laravel `.env` and `storage` directory are writable by PHP. The public subdomain remains `/html/ob-book`.

Browse to `https://ob-book.ais-grp.co.za/setup`. Enter the MySQL password, create the tables, then create the first administrator. After the first administrator is created, setup locks automatically.

## Notes

Do not place the database password in Git or in the deployment ZIP. The browser installer writes it directly into the private `.env` file under `ob-book-app`.
