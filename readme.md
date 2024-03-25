# Synesthesia tests

Docker setup for development and production of the synesthesia tests.

## Environment variables:

```
SYNCONFIGBRANCH=kinderen
PASS=username:encrypted_password

MYSQL_USER=
MYSQL_PASSWORD=
MYSQL_HOST=
MYSQL_PORT=
NGINXPORT=
```

For development:

```
MYSQL_ROOT_PASSWORD=
NODEDEVPORT=2222
```

## Start:

`yarn start`

!Important 

run /api/setup to initialise database

## Build:

start running containers...

`yarn dev`

...followed by:

`yarn build`

## Develop:

`yarn dev`

## Fonts

Roboto Mono (Google Fonts) is used for the graphemes, see [https://github.com/google/fonts](https://github.com/google/fonts) for more information.
