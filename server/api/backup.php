<?php

function export_csv_files()
{
    $dbc = db();

    ensure_directory(TEMP_PATH);

    $timestamp = gmdate("Y.m.d-H.i.s");
    $profilePath = join_paths(TEMP_PATH, "profiles-$timestamp.csv");
    $questionsPath = join_paths(TEMP_PATH, "questions-$timestamp.csv");

    $extraColumns = get_extra_columns();
    $profileColumns = get_profile_columns();
    $allProfileColumns = array_merge($profileColumns, $extraColumns);

    $profileFile = fopen($profilePath, 'w');
    if ($profileFile === false) {
        throw new RuntimeException('Could not open profiles file.');
    }
    fputcsv($profileFile, $allProfileColumns, ',', '"', '');

    $profiletable = db_table("profile");
    $extratable = db_table("extra");
    $query = "SELECT
        $profiletable.created AS created,
        $profiletable.modified AS modified,
        $profiletable.UID AS UID,
        $profiletable.IP AS IP,
        $profiletable.language AS language,
        $profiletable.finishedtests AS finishedtests,
        $profiletable.touchscreen AS touchscreen,
        $profiletable.USERID AS USERID,
        $profiletable.SHARED AS SHARED,
        $extratable.data AS data
        FROM $profiletable
        LEFT JOIN $extratable ON $profiletable.UID = $extratable.UID";

    $res = $dbc->query($query);
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $array = array();
        foreach ($profileColumns as $column) {
            $array[] = $row[$column] ?? "";
        }

        $json = json_decode($row['data'] ?? "", true);
        $json = is_array($json) ? $json : array();
        foreach ($extraColumns as $column) {
            $array[] = array_key_exists($column, $json) ? $json[$column] : "";
        }

        fputcsv($profileFile, $array, ',', '"', '');
    }
    fclose($profileFile);

    $questionsColumns = get_questions_columns();
    $questionsFile = fopen($questionsPath, 'w');
    if ($questionsFile === false) {
        throw new RuntimeException('Could not open questions file.');
    }
    fputcsv($questionsFile, $questionsColumns, ',', '"', '');

    $questionstable = db_table("questions");
    $res = $dbc->query("SELECT * FROM $questionstable");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $line = array();
        foreach ($questionsColumns as $column) {
            $line[] = $row[$column] ?? "";
        }
        fputcsv($questionsFile, $line, ',', '"', '');
    }
    fclose($questionsFile);

    return array(
        'timestamp' => $timestamp,
        'files' => array(
            basename($profilePath) => $profilePath,
            basename($questionsPath) => $questionsPath,
        ),
    );
}

function webdav_url($base, $name)
{
    $segments = array_filter(explode('/', trim($name, '/')), 'strlen');
    $encoded = array_map('rawurlencode', $segments);
    return rtrim($base, '/') . '/' . implode('/', $encoded);
}

function webdav_request($method, $url, $body = null, $contentType = 'application/octet-stream')
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('cURL extension is required for SURFdrive backups.');
    }
    if (!SURFDRIVE_WEBDAV_URL || !SURFDRIVE_USERNAME || !SURFDRIVE_PASSWORD) {
        throw new RuntimeException('SURFdrive WebDAV credentials are not configured.');
    }

    $ch = curl_init($url);
    $headers = array();
    if ($body !== null) {
        $headers[] = 'Content-Type: ' . $contentType;
        $headers[] = 'Content-Length: ' . strlen($body);
    }

    curl_setopt_array($ch, array(
        CURLOPT_USERPWD => SURFDRIVE_USERNAME . ':' . SURFDRIVE_PASSWORD,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ));

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException('WebDAV request failed: ' . $error);
    }

    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    return array('status' => $status, 'body' => $response);
}

function surfdrive_put_file($remoteName, $localPath, $contentType = 'text/csv; charset=utf-8')
{
    if (!SURFDRIVE_WEBDAV_URL || !SURFDRIVE_USERNAME || !SURFDRIVE_PASSWORD) {
        throw new RuntimeException('SURFdrive WebDAV credentials are not configured.');
    }

    $body = file_get_contents($localPath);
    if ($body === false) {
        throw new RuntimeException('Could not read local backup file: ' . $localPath);
    }

    $result = webdav_request('PUT', webdav_url(SURFDRIVE_WEBDAV_URL, $remoteName), $body, $contentType);
    if ($result['status'] < 200 || $result['status'] >= 300) {
        throw new RuntimeException('WebDAV PUT failed for ' . $remoteName . ' with HTTP ' . $result['status']);
    }
}

function run_surfdrive_backup($force = false)
{
    $dbc = db();
    $now = db_now();

    $lastChange = db_get_meta($dbc, 'last_data_change_at');
    $lastBackup = db_get_meta($dbc, 'last_backup_at');
    $lastAttempt = db_get_meta($dbc, 'last_backup_attempt_at');

    $hasChanges = $force || ($lastChange !== null && ($lastBackup === null || strtotime($lastChange) > strtotime($lastBackup)));
    if (!$hasChanges) {
        return array(
            'status' => 'skipped',
            'reason' => 'No changes since last backup.',
            'last_backup_at' => $lastBackup,
        );
    }

    if (!$force && $lastAttempt !== null && (time() - strtotime($lastAttempt)) < 3600) {
        return array(
            'status' => 'skipped',
            'reason' => 'Automatic backup was attempted recently.',
            'last_backup_attempt_at' => $lastAttempt,
        );
    }

    db_set_meta($dbc, 'last_backup_attempt_at', $now);

    $export = export_csv_files();
    $timestamp = $export['timestamp'];
    $cleanup = array_values($export['files']);

    try {
        foreach ($export['files'] as $filename => $localPath) {
            surfdrive_put_file($filename, $localPath);
            $latestName = preg_replace('/-\d{4}\.\d{2}\.\d{2}-\d{2}\.\d{2}\.\d{2}\.csv$/', '-latest.csv', $filename);
            surfdrive_put_file($latestName, $localPath);
        }

        db_set_meta($dbc, 'last_backup_at', $now);
        db_set_meta($dbc, 'last_backup_status', 'success');
        db_set_meta($dbc, 'last_backup_error', '');

        return array(
            'status' => 'success',
            'timestamp' => $timestamp,
            'uploaded_files' => array_keys($export['files']),
            'last_backup_at' => $now,
        );
    } catch (Throwable $e) {
        db_set_meta($dbc, 'last_backup_status', 'failed');
        db_set_meta($dbc, 'last_backup_error', $e->getMessage());
        throw $e;
    } finally {
        foreach ($cleanup as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}

function maybe_run_automatic_backup()
{
    try {
        $result = run_surfdrive_backup(false);
        if (($result['status'] ?? '') === 'success') {
            error_log('Automatic SURFdrive backup completed.');
        }
    } catch (Throwable $e) {
        error_log('Automatic SURFdrive backup failed: ' . $e->getMessage());
    }
}
