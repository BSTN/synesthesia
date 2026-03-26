<?php

function http_head_status($url)
{
    if (!function_exists('curl_init')) {
        return array(
            'ok' => false,
            'message' => 'cURL extension is not available.',
        );
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT => 'synesthesia-diagnostics/1.0',
    ));

    $result = curl_exec($ch);
    if ($result === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return array(
            'ok' => false,
            'message' => $error,
        );
    }

    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    return array(
        'ok' => $status >= 200 && $status < 400,
        'status_code' => $status,
        'message' => 'HTTP ' . $status,
    );
}

function diagnostics_report()
{
    $dataDir = dirname(SQLITE_PATH);
    $tempDir = TEMP_PATH;
    $configDir = CONFIGPATH;

    $report = array(
        'environment' => array(
            'app_env' => APP_ENV,
            'production' => PRODUCTION,
        ),
        'paths' => array(
            'config_path' => array(
                'exists' => is_dir($configDir),
                'readable' => is_readable($configDir),
            ),
            'sqlite_path' => array(
                'exists' => is_file(SQLITE_PATH),
                'parent_writable' => is_dir($dataDir) ? is_writable($dataDir) : is_writable(dirname($dataDir)),
            ),
            'temp_path' => array(
                'exists' => is_dir($tempDir),
                'writable' => is_dir($tempDir) ? is_writable($tempDir) : is_writable(dirname($tempDir)),
            ),
        ),
        'extensions' => array(
            'curl' => function_exists('curl_init'),
            'ziparchive' => class_exists('ZipArchive'),
            'pdo_sqlite' => extension_loaded('pdo_sqlite'),
            'sqlite3' => extension_loaded('sqlite3'),
        ),
        'outbound' => array(
            'github_archive_reachable' => http_head_status(CONFIG_REPO_ARCHIVE_URL),
            'surfdrive_configured' => (bool) (SURFDRIVE_WEBDAV_URL && SURFDRIVE_USERNAME && SURFDRIVE_PASSWORD),
        ),
    );

    $allChecks = array(
        $report['paths']['config_path']['exists'],
        $report['paths']['config_path']['readable'],
        $report['extensions']['curl'],
        $report['extensions']['ziparchive'],
        $report['extensions']['pdo_sqlite'],
        $report['extensions']['sqlite3'],
        $report['outbound']['github_archive_reachable']['ok'],
    );

    $report['ok'] = !in_array(false, $allChecks, true);

    return $report;
}
