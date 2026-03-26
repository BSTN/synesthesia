# Synesthesia tests

Vue 2 + PHP application for synesthesia research tests, built with Vite and stored in SQLite.

## Local development

1. Install JS dependencies with `yarn install`.
2. Install PHP dependencies with `composer install` in `server/`.
3. Copy `.env.example` to `.env` and adjust paths if needed.
4. Start Vite with `yarn dev`.
5. Start PHP with `yarn dev:php`.
6. Open `http://localhost:8000`.

The API will create the SQLite database automatically at first request. You can also hit `/api/setup`.

## Backups

Backups are pushed to SURFdrive over WebDAV:

- automatic backup:
  - checked on normal page loads
  - runs only when data changed since the last successful backup
  - retries at most once per hour if a previous automatic attempt failed
- manual backup:
  - `GET /backup`
  - `GET /api/backup`

Required env vars:

- `SURFDRIVE_WEBDAV_URL`
  - full target WebDAV folder URL
- `SURFDRIVE_USERNAME`
- `SURFDRIVE_PASSWORD`

Each successful backup uploads:

- a timestamped `profiles-*.csv`
- a timestamped `questions-*.csv`
- `profiles-latest.csv`
- `questions-latest.csv`

## Config update

The app can refresh `synesthesia_config` directly from GitHub:

- `GET /update`
- `GET /api/update`

Update workflow:

1. download the GitHub archive for `BSTN/synesthesia_config` `master`
2. validate YAML and expected file structure there
3. only replace the live `synesthesia_config` directory if validation passes

Validation errors are returned with file-specific messages.

Optional env overrides:

- `CONFIG_REPO_URL`
- `CONFIG_REPO_BRANCH`
- `CONFIG_REPO_ARCHIVE_URL`

## Diagnostics

Runtime diagnostics are available at:

- `GET /diagnostics`
- `GET /api/diagnostics`

This reports:

- PHP version / environment
- whether `curl`, `ZipArchive`, `pdo_sqlite`, and `sqlite3` are available
- whether config/data/temp paths are present and writable
- whether GitHub archive download is reachable from the host

## Production/shared hosting

- Use `server/` as the web root if your hosting panel allows it.
- Run `yarn build` before deployment so `server/dist/` contains the frontend assets.
- Run `composer install --no-dev` in `server/`.
- Ensure the directory configured by `DATA_PATH` is writable by PHP.
- Keep `synesthesia_config` deployed next to this repository and point `CONFIGPATH` to it.
- Ensure PHP has:
  - `pdo_sqlite`
  - `sqlite3`
  - `curl`

## Environment variables

`BASE`
- Base path for the application, default `/`.

`CONFIGBASE`
- Public path to the config repository, default `/synesthesia_config`.

`CONFIGPATH`
- Filesystem path to the config repository, default `../synesthesia_config`.

`APP_ORIGIN`
- Vite dev server origin, used only in development.

`APP_ENV`
- Application environment, default `production`. Use `development` locally.

`DATA_PATH`, `SQLITE_PATH`, `TEMP_PATH`
- Writable filesystem paths for the SQLite database and temp/export files.

`SURFDRIVE_WEBDAV_URL`, `SURFDRIVE_USERNAME`, `SURFDRIVE_PASSWORD`
- WebDAV destination and credentials used by backup sync.

## Notes

- Fonts still depend on the configuration/assets already used by the app.
