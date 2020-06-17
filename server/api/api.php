<?php

require_once "../config.php";
require_once "api-functions.php";

/* 
 * Connect to database
 */
try {
    $dbc = new PDO(
        'mysql:host=' . MYSQL_HOST . ';
        dbname=' . MYSQL_DBNAME,
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error("Could not connect to database.");
}

/* 
 * API Start 
 */

if ($PATH === "/create") {

    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    };

    $data = (array) json_decode($input);
    /* add UID */
    $data['UID'] = getUid(64);
    $data['IP'] = get_ip_address();

    $insertdata = array();
    foreach ($data as $k => $v) {
        $insertdata[":" . $k] = $v;
    }

    $prep = $dbc->prepare(
        "INSERT INTO profile SET 
            IP=SHA2(:IP,256), 
            language=:language, 
            UID=:UID"
    );
    $prep->execute($insertdata);

    pjson(array("UID" => $data['UID']));
}

if ($PATH === "/store") {

    /*
    * Store data: post request
    * 
    * @param UID
    * @param data 
    * @param table
    */

    /* 
     * Get json 
     */
    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    };

    $postdata = (array) json_decode($input);

    if (!key_exists("UID", $postdata)) {
        error("Missing UID.");
    }
    if (!key_exists("data", $postdata)) {
        error("Missing data.");
    }
    if (!key_exists("table", $postdata)) {
        error("Missing table.");
    }

    /* 
     * The data object 
     */
    $data = (array) $postdata['data'];

    /*
     * Prepare insert data 
     */
    $insertdata = array();
    foreach ($data as $k => $v) {
        $insertdata[":$k"] = $v;
    }
    $insertdata[':IP'] = get_ip_address();
    $insertdata[':UID'] = $postdata['UID'];

    /* 
     * $postdata['table']: profile / questions / extraquestions
     */
    if ($postdata['table'] === 'profile') {

        /*
         * Insert profile data
         */
        $prep = $dbc->prepare(
            "INSERT INTO profile SET 
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID
            ON DUPLICATE KEY UPDATE
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID
            ;"
        );
        $prep->execute($insertdata);
    } elseif ($postdata['table'] === 'questions') {
        $prep = $dbc->prepare(
            "INSERT INTO questions SET 
                IP=SHA2(:IP,256), 
                UID=:UID,
                testname=:testname,
                setname=:setname,
                symbol=:symbol,
                value=:value,
                clicks=:clicks,
                clicksslider=:clicksslider,
                timing=:timing,
                qnr=:qnr,
                interrupted=:interrupted;"
        );

        $prep->execute($insertdata);
    } elseif ($postdata['table'] === 'extra') {
        $values = $insertdata[':values'];
        unset($insertdata[':values']);

        // get current values (JSON)


        // $prep = $dbc->prepare(
        //     "SET @current := (SELECT data FROM extra WHERE UID=:UID);
        //     INSERT INTO extra SET 
        //       IP=SHA2(:IP,256), 
        //       UID=:UID,
        //       data=JSON_SET('{}', :key, :value)
        //     ON DUPLICATE KEY UPDATE
        //       IP=SHA2(:IP,256),
        //       data=:data;"
        // );

        $data = json_encode($values);
        $prep->execute(array(":data" => $data));
    }


    pjson($data);
}

error("Sorry, path does not exist.");
