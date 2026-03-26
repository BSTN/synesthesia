<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$fullPath = __DIR__ . $path;

if ($path !== '/' && file_exists($fullPath) && !is_dir($fullPath)) {
    return false;
}

if ($path === '/backup') {
    require __DIR__ . '/api/api.php';
    return true;
}

if ($path === '/update') {
    require __DIR__ . '/api/api.php';
    return true;
}

if (strpos($path, '/api') === 0) {
    require __DIR__ . '/api/api.php';
    return true;
}

require __DIR__ . '/index.php';
