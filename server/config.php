<?php

function load_env_file($path)
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        if ($value !== '' && (($value[0] === '"' && substr($value, -1) === '"') || ($value[0] === "'" && substr($value, -1) === "'"))) {
            $value = substr($value, 1, -1);
        }

        putenv("$key=$value");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

function join_paths()
{
    $paths = func_get_args();
    $parts = array();

    foreach ($paths as $index => $path) {
        if ($path === null || $path === '') {
            continue;
        }

        $parts[] = $index === 0 ? rtrim($path, DIRECTORY_SEPARATOR) : trim($path, DIRECTORY_SEPARATOR);
    }

    return implode(DIRECTORY_SEPARATOR, $parts);
}

function env_value($key, $default = null)
{
    $value = getenv($key);
    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    return $value;
}

function normalize_path($path, $base)
{
    if ($path === null || $path === '') {
        return $path;
    }

    if ($path[0] === '/' || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) {
        return $path;
    }

    return join_paths($base, $path);
}

load_env_file(dirname(__DIR__) . '/.env');

define('PRODUCTION', env_value('development', 'false') !== "true");
define('BASE', rtrim(env_value('BASE', '/'), '/').'/');
define('CONFIGBASE', env_value('CONFIGBASE', '/synesthesia_config'));
define('APP_ENV', PRODUCTION ? 'production' : 'development');
define('APP_ORIGIN', rtrim(env_value('APP_ORIGIN', 'http://localhost:' . env_value('NODEDEVPORT', '2222')), '/'));
define('CONFIGPATH', normalize_path(env_value('CONFIGPATH', '../synesthesia_config'), dirname(__DIR__)));
define('DATA_PATH', normalize_path(env_value('DATA_PATH', 'var'), dirname(__DIR__)));
define('SQLITE_PATH', normalize_path(env_value('SQLITE_PATH', join_paths('var', 'synesthesia.sqlite')), dirname(__DIR__)));
define('TEMP_PATH', normalize_path(env_value('TEMP_PATH', join_paths('var', 'tmp')), dirname(__DIR__)));
define('DB_PREFIX', env_value('DB_PREFIX', ''));
define('SURFDRIVE_WEBDAV_URL', env_value('SURFDRIVE_WEBDAV_URL'));
define('SURFDRIVE_USERNAME', env_value('SURFDRIVE_USERNAME'));
define('SURFDRIVE_PASSWORD', env_value('SURFDRIVE_PASSWORD'));
define('CONFIG_REPO_URL', env_value('CONFIG_REPO_URL', 'https://github.com/BSTN/synesthesia_config.git'));
define('CONFIG_REPO_BRANCH', env_value('CONFIG_REPO_BRANCH', 'master'));

if (PRODUCTION) {
    ini_set('display_errors', '0');
} else {
    ini_set('display_errors', '1');
}
error_reporting(E_ALL);

function ensure_directory($path)
{
    if (!is_dir($path) && !mkdir($path, 0775, true) && !is_dir($path)) {
        throw new RuntimeException('Could not create directory: ' . $path);
    }
}

function require_readable_file($path, $label)
{
    if (!is_readable($path)) {
        throw new RuntimeException($label . ' not found or not readable at ' . $path);
    }
}

function vite_manifest()
{
    static $manifest = null;

    if ($manifest !== null) {
        return $manifest;
    }

    $manifestPath = __DIR__ . '/dist/.vite/manifest.json';
    if (!file_exists($manifestPath)) {
        $manifest = array();
        return $manifest;
    }

    $rawManifest = file_get_contents($manifestPath);
    $decoded = json_decode($rawManifest, true);
    $manifest = is_array($decoded) ? $decoded : array();

    return $manifest;
}

function vite_asset_tags($entry)
{
    if (!PRODUCTION) {
        return array(
            '<script type="module" src="' . APP_ORIGIN . '/@vite/client"></script>',
            '<script type="module" src="' . APP_ORIGIN . '/' . ltrim($entry, '/') . '"></script>',
        );
    }

    $manifest = vite_manifest();
    if (!array_key_exists($entry, $manifest)) {
        throw new RuntimeException('Missing Vite manifest entry for ' . $entry);
    }

    $tags = array();
    $entryData = $manifest[$entry];

    if (array_key_exists('css', $entryData)) {
        foreach ($entryData['css'] as $css) {
            $tags[] = '<link rel="stylesheet" href="' . BASE . 'dist/' . ltrim($css, '/') . '">';
        }
    }

    if (array_key_exists('imports', $entryData)) {
        foreach ($entryData['imports'] as $import) {
            if (!array_key_exists($import, $manifest)) {
                continue;
            }

            $tags[] = '<link rel="modulepreload" href="' . BASE . 'dist/' . ltrim($manifest[$import]['file'], '/') . '">';
        }
    }

    $tags[] = '<script type="module" src="' . BASE . 'dist/' . ltrim($entryData['file'], '/') . '"></script>';

    return $tags;
}
