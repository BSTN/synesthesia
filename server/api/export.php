<?php

function export_to_csv()
{
    $dbc = db();

    ensure_directory(TEMP_PATH);

    $timestamp = date("Y.m.d-H.i.s");
    $profilePath = join_paths(TEMP_PATH, "profiles-$timestamp.csv");
    $questionsPath = join_paths(TEMP_PATH, "questions-$timestamp.csv");

    $extraColumns = get_extra_columns();
    $profileColumns = get_profile_columns();
    $allProfileColumns = array_merge($profileColumns, $extraColumns);

    $profileFile = fopen($profilePath, 'w');
    if ($profileFile === false) {
        die('Could not open profiles file.');
    }
    fputcsv($profileFile, $allProfileColumns);

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

        fputcsv($profileFile, $array);
    }
    fclose($profileFile);

    $questionsColumns = get_questions_columns();
    $questionsFile = fopen($questionsPath, 'w');
    if ($questionsFile === false) {
        die('Could not open questions file.');
    }
    fputcsv($questionsFile, $questionsColumns);

    $questionstable = db_table("questions");
    $res = $dbc->query("SELECT * FROM $questionstable");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $line = array();
        foreach ($questionsColumns as $column) {
            $line[] = $row[$column] ?? "";
        }
        fputcsv($questionsFile, $line);
    }
    fclose($questionsFile);

    $files = array($profilePath, $questionsPath);
    $zipPath = join_paths(TEMP_PATH, 'bundled-' . date("Y.m.d-H.i.s") . ".zip");
    $zip = new ZipArchive();
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    foreach ($files as $file) {
        $zip->addFile($file, basename($file));
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . 'synesthesia-data-' . date("Y.m.d-H.i.s") . ".zip");
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);

    unlink($profilePath);
    unlink($questionsPath);
    unlink($zipPath);

    exit();
}

function get_extra_columns()
{
    $dbc = db();
    $table = db_table("extra");
    $prep = $dbc->prepare("SELECT data FROM $table");
    $prep->execute();
    $res = $prep->fetchAll(PDO::FETCH_COLUMN, 0);
    $extras = array();
    foreach ($res as $v) {
        $json = json_decode($v, true);
        if (is_array($json)) {
            foreach ($json as $key => $value) {
                $extras[$key] = $key;
            }
        }
    }
    return array_keys($extras);
}

function get_profile_columns()
{
    return db_table_columns(db(), db_table("profile"));
}

function get_questions_columns()
{
    return db_table_columns(db(), db_table("questions"));
}
