# AGENTS.md

## Purpose

This repository is the `synesthesia` application. It is a Vue 2 + PHP app for running synesthesia research tests.

There are two sibling repositories in the parent folder:

- `synesthesia`: this app
- `synesthesia_config`: researcher-managed content and test definitions

Agents working here should treat `synesthesia_config` as external content. Do not modify it unless the user explicitly asks.

## Current Architecture

### Frontend

- Vue 2.7 app built with Vite
- Entry point: [app/index.js](/Users/bok/node/work/synesthesia/synesthesia/app/index.js)
- Router: [app/router.js](/Users/bok/node/work/synesthesia/synesthesia/app/router.js)
- Main app shell: [app/index.vue](/Users/bok/node/work/synesthesia/synesthesia/app/index.vue)
- State management: Vuex modules in `app/stores/`

Important frontend facts:

- Components and directives are auto-registered with `import.meta.glob`.
- `vue-slider-component` must be imported from:
  - `vue-slider-component/dist/vue-slider-component.common.js`
- Vite must resolve `vue` to:
  - `vue/dist/vue.common.js`
  This avoids `*.extend is not a function` runtime failures from `vue-class-component` consumers.

### Backend

- PHP entrypoint: [server/index.php](/Users/bok/node/work/synesthesia/synesthesia/server/index.php)
- Local dev router: [server/router.php](/Users/bok/node/work/synesthesia/synesthesia/server/router.php)
- API entrypoint: [server/api/api.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/api.php)
- SQLite helpers and schema bootstrap: [server/api/db.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/db.php)
- Runtime config helpers: [server/config.php](/Users/bok/node/work/synesthesia/synesthesia/server/config.php)

The backend no longer uses Docker, Nginx, MySQL, or MariaDB.

### Content Source

The app loads runtime content from `synesthesia_config`:

- `config.yml`
- `translations.yml`
- `tests/*.yml`
- `texts/*.md`

These are read server-side in [server/index.php](/Users/bok/node/work/synesthesia/synesthesia/server/index.php) and injected into the HTML as bootload JSON/templates.

## Request Flow

### Main page

1. Request hits `server/router.php`.
2. Non-file, non-API requests are routed to `server/index.php`.
3. `server/index.php`:
   - loads `.env` via `server/config.php`
   - auto-creates the SQLite DB/schema if the DB file does not exist
   - loads YAML/Markdown from `synesthesia_config`
   - emits Vite assets
4. Vue app boots in the browser.

### API

`/api/...` routes are handled by `server/api/api.php`.

Main endpoints:

- `/api/setup`
  - ensures schema exists
- `/api/backup`
  - pushes CSV backups to SURFdrive immediately
- `/backup`
  - alias for the same immediate backup trigger
- `/api/create`
  - creates a profile row and returns `UID` and `SHARED`
- `/api/store`
  - stores one of:
    - `profile`
    - `questions`
    - `extra`
- `/api/getshared`
  - returns shared result data by share code
`/api/update` is intentionally disabled and returns `501`.

## Data Model

SQLite tables are created in [server/api/db.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/db.php):

- `profile`
  - one row per participant/session
  - stores `UID`, `SHARED`, language, finished tests, touchscreen, optional `USERID`
- `questions`
  - one row per answered question
- `extra`
  - one row per UID, JSON payload for extra/likert data
- `access`
  - legacy table from the old download flow; no longer part of the active feature set
- `app_meta`
  - key/value metadata used for backup state
  - tracks last data change and backup timestamps/status

IP addresses are stored as SHA-256 hashes, not plain text.

## Local Persistence

By default local data is stored at:

- DB: [var/synesthesia.sqlite](/Users/bok/node/work/synesthesia/synesthesia/var/synesthesia.sqlite)
- temp exports: `var/tmp`

These come from `.env`:

- `DATA_PATH`
- `SQLITE_PATH`
- `TEMP_PATH`

## Local Development

Install:

```bash
yarn install
cd server && composer install && cd ..
```

Run:

```bash
yarn dev
yarn dev:php
```

Open:

```text
http://localhost:8000
```

Important local assumptions:

- `synesthesia_config` sits next to this repo, not inside it.
- `CONFIGPATH` should usually be `../synesthesia_config`.
- `yarn dev:php` uses the PHP router script so `/api/...` works locally.

## Build And Deployment

Build command:

```bash
yarn build
```

Output:

- built frontend assets go to `server/dist`
- manifest path: `server/dist/.vite/manifest.json`

Production assumptions:

- shared hosting deployment
- `server/` can be used as web root
- writable filesystem access is required for SQLite and temp exports
- no container runtime is expected

## Frontend Logic Notes

### Boot data

The frontend reads boot data from HTML script tags:

- `bootload-config`
- `bootload-translations`
- `bootload-tests`
- `template*` markdown blocks

If these are missing, the app will not boot correctly.

### Store flow

The most important Vuex modules are:

- `profile`
  - tracks `UID`, `USERID`, `SHARED`, language, touchscreen, finished tests
  - uploads profile state to `/api/store`
- `tests`
  - prepares questions from test YAML
  - tracks current test, position, answers, page count
  - advances through pretest/test/posttest flow
- `extra`
  - stores extra/likert answers
- `shared`
  - stores fetched shared result sets

### Test lifecycle

Typical user flow:

1. Home page loads available tests from bootloaded YAML.
2. Entering a test may create a profile via `/api/create`.
3. Answers are tracked in Vuex.
4. Question rows are stored through `/api/store`.
5. Finishing a test updates `profile.finishedtests`.
6. Extra/likert answers are stored in `extra.data` as JSON.
7. Results pages compute and display scores from stored answers.

## Known Constraints And Gotchas

### Do not reintroduce old infra

This repo intentionally removed:

- Docker
- docker-compose
- nginx
- MariaDB/MySQL
- webpack

Do not add them back unless explicitly requested.

### Be careful with Vue imports

The current Vite setup contains compatibility workarounds for legacy Vue 2 packages. If you change them casually, you may reintroduce runtime errors like:

- `*.extend is not a function`

Relevant files:

- [vite.config.mjs](/Users/bok/node/work/synesthesia/synesthesia/vite.config.mjs)
- [app/index.js](/Users/bok/node/work/synesthesia/synesthesia/app/index.js)

### Be careful with PHP includes

Use `__DIR__`-based includes in PHP. Relative includes based on current working directory caused routing bugs before.

### `synesthesia_config` is not this repo

If app behavior seems driven by missing copy, translations, or test definitions, inspect `synesthesia_config` paths and contents first instead of changing app code blindly.

### Production asset URLs

Vite is configured with relative asset output because built files are served from `/dist/...`, not the web root.

### Backup behavior

- Backups are pushed to SURFdrive WebDAV, not downloaded manually.
- Automatic backup is opportunistic:
  - it is checked during normal page loads
  - it runs only when data changed since the last successful backup
  - without traffic, no automatic backup will run
- Immediate manual trigger:
  - `/backup`
  - `/api/backup`
- Required env vars:
  - `SURFDRIVE_WEBDAV_URL`
  - `SURFDRIVE_USERNAME`
  - `SURFDRIVE_PASSWORD`

## Useful Files

- [vite.config.mjs](/Users/bok/node/work/synesthesia/synesthesia/vite.config.mjs)
- [package.json](/Users/bok/node/work/synesthesia/synesthesia/package.json)
- [readme.md](/Users/bok/node/work/synesthesia/synesthesia/readme.md)
- [server/index.php](/Users/bok/node/work/synesthesia/synesthesia/server/index.php)
- [server/router.php](/Users/bok/node/work/synesthesia/synesthesia/server/router.php)
- [server/config.php](/Users/bok/node/work/synesthesia/synesthesia/server/config.php)
- [server/api/api.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/api.php)
- [server/api/backup.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/backup.php)
- [server/api/db.php](/Users/bok/node/work/synesthesia/synesthesia/server/api/db.php)
- [app/index.js](/Users/bok/node/work/synesthesia/synesthesia/app/index.js)
- [app/router.js](/Users/bok/node/work/synesthesia/synesthesia/app/router.js)
- [app/stores/profile.js](/Users/bok/node/work/synesthesia/synesthesia/app/stores/profile.js)
- [app/stores/tests.js](/Users/bok/node/work/synesthesia/synesthesia/app/stores/tests.js)

## Agent Recommendations

- Prefer minimal, targeted changes.
- Preserve compatibility with shared hosting and SQLite.
- Verify both:
  - PHP side (`php -l ...`)
  - frontend build (`yarn build`)
- If behavior is content-driven, inspect `synesthesia_config` before changing code.
- When touching routing or bootstrapping, verify both `/` and `/api/setup` locally.
