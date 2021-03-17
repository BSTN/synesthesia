# dev dependencies

gulp

# environment setup

`.env:`

```
DEV_BUILD_PATH=/abs/path
DEV_TEMP_PATH=/abs/path
DEV_BASE=/web/path/
DEV_MYSQL_USERNAME=
DEV_MYSQL_PASSWORD=
DEV_MYSQL_PORT=
DEV_MYSQL_HOST=localhost
DEV_MYSQL_DBNAME=
DEV_HOTPORT=3000

LIVE_BUILD_PATH=./live
LIVE_TEMP_PATH=/abs/path
LIVE_BASE=/
LIVE_MYSQL_USERNAME=
LIVE_MYSQL_PASSWORD=
LIVE_MYSQL_PORT=
LIVE_MYSQL_HOST=
LIVE_MYSQL_DBNAME=

SSH_HOST=
SSH_PORT=
SSH_USERNAME=
SSH_PRIVATEKEYPATH=
SSH_PATH=

DB_PREFIX=TEST

DEVPASS=
LIVEPASS=

```

# create new login credentials

`htpasswd -nB username`

# Server-side installation

Install composer components:

```
composer install
```

# Fonts

Thanks to [Kometa](https://kometa.xyz) for using their font [Victor Serif](https://www.kometa.xyz/typefaces/victor-serif/).

Roboto Mono (Google Fonts) is used for the graphemes, see [https://github.com/google/fonts](https://github.com/google/fonts) for more information.
