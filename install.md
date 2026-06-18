# TransIP Shared Hosting Installation

This guide describes how to deploy the Synesthesia app to a TransIP shared hosting package.

## Requirements

- PHP with these extensions enabled:
  - `pdo_sqlite`
  - `sqlite3`
  - `curl`
  - `zip` / `ZipArchive`
- SFTP access to the TransIP hosting package.
- A writable directory outside the public web root for SQLite data and temporary CSV exports.
- A built frontend in `server/dist`.
- Composer dependencies installed in `server/vendor`.
- A deployed `synesthesia_config` directory containing:
  - `config.yml`
  - `translations.yml`
  - `tests/*.yml`
  - `texts/*.md`

## Recommended Directory Layout

Use `server/` as the public document root if the TransIP control panel allows this.

```text
private-or-parent-directory/
  synesthesia_config/
  synesthesia-data/
    synesthesia.sqlite
    tmp/

public-document-root/
  .htaccess
  index.php
  config.php
  robots.txt
  api/
  assets/
  dist/
  vendor/
```

If TransIP does not allow changing the document root, upload the contents of `synesthesia/server/` into the public web directory.

Keep `synesthesia_config` and `synesthesia-data` outside the public web root when possible.

## Build Locally

Run these commands on your local machine before uploading:

```bash
cd /path/to/synesthesia
npm install
npm run build

cd server
composer install --no-dev --optimize-autoloader
```

The build command writes frontend assets to `server/dist`.

The Composer command writes PHP dependencies to `server/vendor`.

## Upload Files

Upload these from `synesthesia/server/` to the public web root:

```text
.htaccess
index.php
config.php
robots.txt
api/
assets/
dist/
vendor/
```

Upload the external configuration repository to:

```text
../synesthesia_config/
```

or another private path of your choice.

Create a writable data directory:

```text
../synesthesia-data/
../synesthesia-data/tmp/
```

## Environment File

Create `.env` in the same directory as `index.php` and `config.php` on the server.

Example for deployment at the domain root:

```env
APP_ENV=production
BASE=/
CONFIGBASE=/synesthesia_config
CONFIGPATH=../synesthesia_config
DATA_PATH=../synesthesia-data
SQLITE_PATH=../synesthesia-data/synesthesia.sqlite
TEMP_PATH=../synesthesia-data/tmp

SURFDRIVE_WEBDAV_URL=https://surfdrive.surf.nl/files/remote.php/nonshib-webdav/synesthesia
SURFDRIVE_USERNAME=your-surfdrive-username
SURFDRIVE_PASSWORD=your-surfdrive-password
```

If the app is deployed in a subdirectory, for example `https://example.nl/synesthesia/`, use:

```env
APP_ENV=production
BASE=/synesthesia/
CONFIGBASE=/synesthesia_config
CONFIGPATH=../synesthesia_config
DATA_PATH=../synesthesia-data
SQLITE_PATH=../synesthesia-data/synesthesia.sqlite
TEMP_PATH=../synesthesia-data/tmp
```

## Permissions

The PHP process must be able to write to:

```text
../synesthesia-data/
../synesthesia-data/tmp/
```

The app creates the SQLite database automatically on first request if it does not already exist.

## First Launch Checks

Open:

```text
https://your-domain.nl/
```

Then open diagnostics:

```text
https://your-domain.nl/api/diagnostics
```

Check that diagnostics reports:

- config path exists and is readable
- SQLite parent directory is writable
- temp directory is writable
- `curl` is available
- `ZipArchive` is available
- `pdo_sqlite` is available
- `sqlite3` is available

Then complete a test submission in the browser and confirm that the SQLite database file appears at:

```text
../synesthesia-data/synesthesia.sqlite
```

## Backup Check

If SURFdrive credentials are configured, trigger a manual backup:

```text
https://your-domain.nl/api/backup
```

A successful response should include:

```json
{
  "status": "success"
}
```

The backup uploads:

- `profiles-*.csv`
- `questions-*.csv`
- `profiles-latest.csv`
- `questions-latest.csv`

## Config Update Check

To refresh `synesthesia_config` from the configured GitHub archive:

```text
https://your-domain.nl/api/update
```

The update process:

1. Downloads the configured archive.
2. Extracts it to a temporary directory.
3. Validates YAML and expected files.
4. Replaces the live config directory only if validation succeeds.

## Troubleshooting

If the homepage returns a configuration error, check:

- `CONFIGPATH` points to the correct filesystem path.
- `config.yml` and `translations.yml` are readable.
- `tests/` and `texts/` exist inside `synesthesia_config`.

If API routes return 404, check:

- `.htaccess` was uploaded.
- Apache rewrite rules are enabled on the hosting package.
- The public document root points to the uploaded `server/` contents.

If data is not saved, check:

- `SQLITE_PATH` points to a writable location.
- The parent directory of `SQLITE_PATH` exists.
- PHP has `pdo_sqlite` and `sqlite3`.

If backups fail, check:

- PHP has `curl`.
- SURFdrive credentials are correct.
- `TEMP_PATH` exists and is writable.
- The WebDAV URL points to the target folder, not just the account root.
