$ErrorActionPreference = 'Stop'
$LaravelRoot = Split-Path -Parent $PSScriptRoot
$BuildRoot = Join-Path $LaravelRoot 'build\shared-hosting'
$AppRoot = Join-Path $BuildRoot 'ob-book-app'
$PublicRoot = Join-Path $BuildRoot 'html\ob-book'
Write-Host 'Building OB Entry Book shared-hosting package...'
if (Test-Path $BuildRoot) { Remove-Item $BuildRoot -Recurse -Force }
New-Item $AppRoot -ItemType Directory -Force | Out-Null
New-Item $PublicRoot -ItemType Directory -Force | Out-Null
$exclude = @('vendor','node_modules','build','.git','.env','tests','public')
Get-ChildItem $LaravelRoot -Force | Where-Object { $exclude -notcontains $_.Name } | ForEach-Object { Copy-Item $_.FullName $AppRoot -Recurse -Force }
Copy-Item (Join-Path $LaravelRoot 'composer.json') $AppRoot -Force
if (Test-Path (Join-Path $LaravelRoot 'composer.lock')) { Copy-Item (Join-Path $LaravelRoot 'composer.lock') $AppRoot -Force }
Write-Host 'Installing production Composer dependencies...'
composer install --working-dir="$AppRoot" --no-dev --optimize-autoloader --no-interaction
Copy-Item (Join-Path $LaravelRoot 'public\*') $PublicRoot -Recurse -Force
Copy-Item (Join-Path $PSScriptRoot 'shared-hosting-index.php') (Join-Path $PublicRoot 'index.php') -Force
$keyBytes = New-Object byte[] 32
[System.Security.Cryptography.RandomNumberGenerator]::Fill($keyBytes)
$appKey = 'base64:' + [Convert]::ToBase64String($keyBytes)
$env = @"
APP_NAME="OB Entry Book"
APP_ENV=production
APP_KEY=$appKey
APP_DEBUG=false
APP_URL=https://ob-book.ais-grp.co.za
APP_TIMEZONE=Africa/Johannesburg
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
LOG_CHANNEL=stack
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=rdtzzbgb_ob_entry_book
DB_USERNAME=rdtzzbgb_ob_app
DB_PASSWORD=
SESSION_DRIVER=file
SESSION_LIFETIME=480
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
CACHE_STORE=file
QUEUE_CONNECTION=sync
"@
Set-Content -Path (Join-Path $AppRoot '.env') -Value $env -Encoding UTF8
New-Item (Join-Path $AppRoot 'storage\app') -ItemType Directory -Force | Out-Null
New-Item (Join-Path $AppRoot 'storage\framework\cache') -ItemType Directory -Force | Out-Null
New-Item (Join-Path $AppRoot 'storage\framework\sessions') -ItemType Directory -Force | Out-Null
New-Item (Join-Path $AppRoot 'storage\framework\views') -ItemType Directory -Force | Out-Null
New-Item (Join-Path $AppRoot 'storage\logs') -ItemType Directory -Force | Out-Null
$zipPath = Join-Path $LaravelRoot 'build\ob-entry-book-demo.zip'
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }
Compress-Archive -Path (Join-Path $BuildRoot '*') -DestinationPath $zipPath -Force
Write-Host "Done: $zipPath"
Write-Host 'Extract the ZIP inside /home/rdtzzbgb/ais-grp.co.za/'
Write-Host 'Then browse to https://ob-book.ais-grp.co.za/setup'
