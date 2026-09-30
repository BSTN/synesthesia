# TransIP Shared Hosting Installation

This guide deploys the app at `https://your-domain.nl/synesthesia/` with TransIP's document root set to `www/public`. Keep `.env` and SQLite data in `www`, outside the public document root.

## Requirements

- PHP 8.2 or newer with `pdo_sqlite`, `sqlite3`, `curl`, and `zip` / `ZipArchive` enabled.
- SFTP access to the hosting package.
- Built frontend assets in `server/dist` and Composer dependencies in `server/vendor`.
- A `synesthesia_config` directory with `config.yml`, `translations.yml`, `tests/*.yml`, `texts/*.md`, and any referenced audio/images.

## Directory Layout

```text
www/
  .env
  data/
    synesthesia.sqlite       # created by the app on first request
    tmp/
  public/                    # TransIP document root
    synesthesia/             # https://your-domain.nl/synesthesia/
      .htaccess
      index.php
      config.php
      api/
      assets/
      dist/
      vendor/
      synesthesia_config/
```

Upload the contents of `synesthesia/server/` to `www/public/synesthesia/`. Upload the configuration repository to `www/public/synesthesia/synesthesia_config/`, omitting its `.git` directory. The app loads YAML/Markdown from this folder, and the browser requests audio/images from it.

In TransIP's control panel, set the domain's document root (website path) to `public`, so `www/public` is public and `www/.env` and `www/data` are not. If another site already uses the domain root, its public files must also live under `www/public` before changing that setting.

## Build Locally

Run these commands before uploading:

```bash
cd /path/to/synesthesia
yarn install --frozen-lockfile
yarn build

cd server
composer install --no-dev --optimize-autoloader
```

Upload `server/.htaccess`, `index.php`, `config.php`, `robots.txt`, `api/`, `assets/`, `dist/`, and `vendor/`. Rebuild and re-upload `dist/` after frontend changes.

## Environment File

Create `www/.env` with these settings:

```env
APP_ENV=production
BASE=/synesthesia/
CONFIGBASE=/synesthesia/synesthesia_config
CONFIGPATH=synesthesia/synesthesia_config
DATA_PATH=../data
SQLITE_PATH=../data/synesthesia.sqlite
TEMP_PATH=../data/tmp

SURFDRIVE_WEBDAV_URL="your-full-WebDAV-folder-URL"
SURFDRIVE_USERNAME="your-WebDAV-username"
SURFDRIVE_PASSWORD="your-WebDAV-password"
```

The app reads `.env` one directory above `config.php` when present, or two directories above it otherwise. In this layout, `config.php` is in `www/public/synesthesia/`, so the second location is `www/.env`. Do not place another `.env` in `www/public/`, as it would take precedence.

The `CONFIGPATH`, `SQLITE_PATH`, and `TEMP_PATH` values above are relative to `www/public/`. Create `www/data/tmp/` and make `www/data/` and `www/data/tmp/` writable by PHP.

## Verify the Installation

1. Open `https://your-domain.nl/synesthesia/` and confirm styles, pages, and tests load.
2. Open `https://your-domain.nl/synesthesia/api/diagnostics`. Check that the config path exists, SQLite and temp parents are writable, and the required PHP extensions are available.
3. Navigate directly to `https://your-domain.nl/synesthesia/results` and then back to the home page. This verifies the router and Apache rewrite for the subpath.
4. Complete a test and verify that `/synesthesia/api/create` and `/synesthesia/api/store` succeed. Confirm `www/data/synesthesia.sqlite` exists.
5. Play audio and check images. Their URLs should start with `/synesthesia/synesthesia_config/`.
6. If SURFdrive is configured, open `https://your-domain.nl/synesthesia/api/backup` and confirm a `success` response and new CSV files in SURFdrive.
7. Confirm that `https://your-domain.nl/.env` and `https://your-domain.nl/data/synesthesia.sqlite` do not return file contents.

To refresh the configuration from the configured GitHub archive, use `https://your-domain.nl/synesthesia/api/update`. This replaces the live configuration folder after validation.

## Troubleshooting

- Blank page or missing JavaScript/CSS: check `APP_ENV=production`, upload the latest `server/dist/`, and inspect requests for `/synesthesia/dist/` in the browser's Network tab.
- Homepage works but navigation fails: check `BASE=/synesthesia/`, upload the latest frontend build and `server/.htaccess`, and verify the website path is `www/public`.
- API returns HTML or 404: upload the latest `server/index.php`, `server/api/`, and `.htaccess`; then test `/synesthesia/api/diagnostics` again.
- Missing audio/images: check `CONFIGBASE=/synesthesia/synesthesia_config` and that the config directory contains the referenced files.
- `surfdrive_configured` is `false`: check that `www/.env` exists, is readable by PHP, and contains all three `SURFDRIVE_` settings.
- Database errors: check `SQLITE_PATH=../data/synesthesia.sqlite`, `pdo_sqlite`, and write access to `www/data`.
