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

## Production/shared hosting

- Use `server/` as the web root if your hosting panel allows it.
- Run `yarn build` before deployment so `server/dist/` contains the frontend assets.
- Run `composer install --no-dev` in `server/`.
- Ensure the directory configured by `DATA_PATH` is writable by PHP.
- Keep `synesthesia_config` deployed next to this repository and point `CONFIGPATH` to it.

## Environment variables

`PASS`
- Download credentials in the form `username:password_hash`.

`BASE`
- Base path for the application, default `/`.

`CONFIGBASE`
- Public path to the config repository, default `/synesthesia_config`.

`CONFIGPATH`
- Filesystem path to the config repository, default `synesthesia_config` next to this repo.

`APP_ORIGIN`
- Vite dev server origin, used only in development.

`DATA_PATH`, `SQLITE_PATH`, `TEMP_PATH`
- Writable filesystem paths for the SQLite database and exports.

## Notes

- The old Docker, Nginx, and MariaDB setup has been removed.
- Fonts still depend on the configuration/assets already used by the app.
