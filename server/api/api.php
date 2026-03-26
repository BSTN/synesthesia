<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Michelf\Markdown;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/api-functions.php";

try {
    $dbc = db();
    db_setup_schema($dbc);
} catch (PDOException $e) {
    echo "Could not connect to database:\n $e";
    error_log("Could not connect to database:\n $e");
    exit();
}

if ($PATH === "/setup") {
    try {
        db_setup_schema($dbc);
    } catch (PDOException $exception) {
        error($exception);
    }
    echo "done.";
    exit();
}

if ($PATH === "/backup") {
    try {
        $result = run_surfdrive_backup(true);
        pjson($result);
    } catch (Throwable $e) {
        error('Backup failed: ' . $e->getMessage());
    }
}

if ($PATH === "/create") {
    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    }

    $data = (array) json_decode($input, true);
    $now = db_now();
    $record = array(
        'UID' => getUid(),
        'IP' => db_hash_ip(get_ip_address()),
        'language' => $data['language'] ?? null,
        'USERID' => $data['USERID'] ?? null,
        'SHARED' => getShared(),
        'created' => $now,
        'modified' => $now,
        'finishedtests' => null,
        'touchscreen' => null,
    );

    db_upsert_profile($dbc, $record);
    db_mark_data_changed($dbc);
    pjson(array("UID" => $record['UID'], "SHARED" => $record['SHARED']));
}

if ($PATH === "/getshared") {
    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    }

    $data = (array) json_decode($input, true);
    if (!array_key_exists('code', $data)) {
        error("Missing shared code.");
    }

    $profileTable = db_table("profile");
    $prep = $dbc->prepare("SELECT finishedtests, UID FROM $profileTable WHERE SHARED = :shared LIMIT 1");
    $prep->execute(array(':shared' => $data['code']));
    $fetch = $prep->fetch();
    if ($fetch === false) {
        pjson('No finished tests found.');
    }

    $finished = array_filter(explode(",", $fetch['finishedtests'] ?? ""));
    $uid = $fetch['UID'];
    $results = array();

    $filter = function ($values) {
        $newobj = array();
        foreach ($values as $k => $vv) {
            if (!in_array($k, array("IP", "UID", "USERID", "ID", "created", "modified"), true)) {
                $newobj[$k] = $vv;
            }
        }
        return $newobj;
    };

    $questionsTable = db_table("questions");
    foreach ($finished as $testname) {
        $prep = $dbc->prepare("SELECT * FROM $questionsTable WHERE testname = :testname AND UID = :UID");
        $prep->execute(array(":testname" => $testname, ":UID" => $uid));
        $fetched = $prep->fetchAll();
        $results[$testname] = $fetched ? array_map($filter, $fetched) : false;
    }

    $extraTable = db_table("extra");
    $prep = $dbc->prepare("SELECT * FROM $extraTable WHERE UID = :UID");
    $prep->execute(array(":UID" => $uid));
    $fetched = $prep->fetch();
    $results['_extra'] = $fetched ? json_decode($fetched['data']) : false;

    pjson($results);
}

if ($PATH === "/update") {
    http_response_code(501);
    echo "Config repository auto-update has been removed. Deploy updated config files via git or your hosting control panel.";
    exit();
}

if ($PATH === "/store") {
    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    }

    $postdata = (array) json_decode($input, true);

    if (!array_key_exists("UID", $postdata)) {
        error("Missing UID.");
    }
    if (!array_key_exists("data", $postdata)) {
        error("Missing data.");
    }
    if (!array_key_exists("table", $postdata)) {
        error("Missing table.");
    }

    $uid = $postdata['UID'];
    $data = (array) $postdata['data'];
    $now = db_now();
    $hashedIp = db_hash_ip(get_ip_address());

    if ($postdata['table'] === 'profile') {
        db_upsert_profile($dbc, array(
            'UID' => $uid,
            'created' => $now,
            'modified' => $now,
            'IP' => $hashedIp,
            'language' => $data['language'] ?? null,
            'finishedtests' => $data['finishedtests'] ?? null,
            'touchscreen' => $data['touchscreen'] ?? null,
            'USERID' => $data['USERID'] ?? null,
            'SHARED' => null,
        ));
        db_mark_data_changed($dbc);
    } elseif ($postdata['table'] === 'questions') {
        db_insert_question($dbc, array(
            'created' => $now,
            'modified' => $now,
            'IP' => $hashedIp,
            'UID' => $uid,
            'testname' => $data['testname'] ?? null,
            'setname' => $data['setname'] ?? null,
            'symbol' => isset($data['symbol']) && is_array($data['symbol']) ? json_encode($data['symbol']) : ($data['symbol'] ?? null),
            'value' => isset($data['value']) && is_array($data['value']) ? json_encode($data['value']) : ($data['value'] ?? null),
            'clicks' => $data['clicks'] ?? null,
            'clicksslider' => $data['clicksslider'] ?? null,
            'position' => isset($data['position']) && is_array($data['position']) ? json_encode($data['position']) : ($data['position'] ?? null),
            'timing' => $data['timing'] ?? null,
            'qnr' => $data['qnr'] ?? null,
        ));
        db_mark_data_changed($dbc);
    } elseif ($postdata['table'] === 'extra') {
        $profileTable = db_table("profile");
        $prep = $dbc->prepare("SELECT UID FROM $profileTable WHERE UID = :UID");
        $prep->execute(array(':UID' => $uid));
        if (!$prep->fetchColumn()) {
            error("Profile missing for UID.");
        }

        $values = isset($data['values']) && is_array($data['values']) ? $data['values'] : array();
        $merged = array_merge(db_existing_extra($dbc, $uid), $values);

        db_upsert_extra($dbc, array(
            'UID' => $uid,
            'created' => $now,
            'modified' => $now,
            'IP' => $hashedIp,
            'data' => json_encode($merged),
        ));
        db_mark_data_changed($dbc);

        pjson("done");
    } else {
        error("Invalid table.");
    }

    pjson($data);
}

error("Sorry, path does not exist.");
