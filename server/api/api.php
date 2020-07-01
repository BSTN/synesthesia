<?php

require_once dirname(__DIR__) .  "/vendor/autoload.php";

use Michelf\Markdown;

require_once "../config.php";

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

require_once "api-functions.php";

/* 
* API Start 
*/

if ($PATH === "/download") {
    if (array_key_exists("passwd", $_POST)) {

        if (brute_check()) {
            error_log("Download: too many attempts.");
            $message = "Login failed, too many attempts. Locked out for 10 minutes.";
            include_once("download.php");
            exit();
        }

        // always sleep a bit
        sleep(1);
        // check password & username
        if ($_POST['name'] === explode(":", PASS)[0] && password_verify($_POST['passwd'], explode(":", PASS)[1])) {
            brute_reset();
            error_log("Download success.");
            export_to_csv();
            exit();
        } else {
            brute_fail();
            error_log("Download login failed.");
            $message = "Login failed, please try again.";
            include_once("download.php");
            exit();
        }
    } else {
        $message = "";
        include_once("download.php");
        exit();
    }
}

if ($PATH === "/setup") {
    $mysqlversion = intval($dbc->query('select version()')->fetchColumn());
    $change = $mysqlversion > 5 ? true : false;
    $sql = file_get_contents(dirname(__DIR__) . "/setup/setup.sql");
    $sql = preg_replace("/CREATE TABLE `/i", "CREATE TABLE " . '`' . DB_PREFIX, $sql);
    if ($change) {
        // change for mysql 8
        $sql = preg_replace("/DEFAULT\ 0/i", "DEFAULT CURRENT_TIMESTAMP", $sql);
    }
    $sql = "USE " . MYSQL_DBNAME . ";" . $sql;
    try {
        $dbc->exec($sql);
    } catch (PDOException $Exception) {
        error($Exception);
    };
    // echo $sql;
    echo "done.";
    exit();
}

if ($PATH === "/create") {

    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    };

    $data = (array) json_decode($input);
    /* add UID */
    $data['UID'] = getUid(64);
    $data['IP'] = get_ip_address();
    $data['SHARED'] = getShared();

    $insertdata = array();
    foreach ($data as $k => $v) {
        $insertdata[":" . $k] = $v;
    }

    $prep = $dbc->prepare(
        "INSERT INTO " . DB_PREFIX . "profile SET 
            created=NOW(),
            IP=SHA2(:IP,256), 
            language=:language, 
            UID=:UID,
            SHARED=:SHARED"
    );
    $prep->execute($insertdata);

    pjson(array("UID" => $data['UID'], "SHARED" => $data['SHARED']));
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
            "INSERT INTO " . DB_PREFIX . "profile SET 
                created=NOW(),
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID
            ON DUPLICATE KEY UPDATE
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID
            ;"
        );
        try {
            $prep->execute($insertdata);
        } catch (PDOException $Exception) {
            error($Exception);
        }
    } elseif ($postdata['table'] === 'questions') {
        $prep = $dbc->prepare(
            "INSERT INTO " . DB_PREFIX . "questions SET
                created=NOW(), 
                IP=SHA2(:IP,256), 
                UID=:UID,
                testname=:testname,
                setname=:setname,
                symbol=:symbol,
                value=:value,
                clicks=:clicks,
                clicksslider=:clicksslider,
                timing=:timing,
                position=:position,
                qnr=:qnr;"
        );


        try {
            $prep->execute($insertdata);
        } catch (PDOException $Exception) {
            error_log("save data:" . print_r($insertdata, true));
            error($Exception);
        }
    } elseif ($postdata['table'] === 'extra') {
        $values = (array) $insertdata[':values'];
        unset($insertdata[':values']);

        // make sure profile exists
        try {
            $prep = $dbc->prepare("SELECT UID FROM " . DB_PREFIX . "profile WHERE UID=:UID;");
            $res = $prep->execute(array(":UID" => $insertdata[':UID']));
        } catch (PDOException $Exception) {
            error($Exception);
        }

        // get JSON
        try {
            $prep = $dbc->prepare("SELECT data FROM " . DB_PREFIX . "extra WHERE UID=:UID;");
            $prep->execute(array(":UID" => $insertdata[':UID']));
            $data = $prep->fetchColumn();
            $data = (array) json_decode($data);
        } catch (PDOException $Exception) {
            $data = array();
        }

        // merge values
        $data = array_merge($data, $values);
        $data = json_encode($data);

        $prep = $dbc->prepare(
            "SET @current := (SELECT data FROM " . DB_PREFIX .
                "extra WHERE UID=:UID);
            INSERT INTO " . DB_PREFIX . "extra SET
              created=NOW(), 
              IP=SHA2(:IP,256), 
              UID=:UID,
              data=:data
            ON DUPLICATE KEY UPDATE
              IP=SHA2(:IP,256),
              data=:data;"
        );

        try {
            $prep->execute(array(":data" => $data, ":IP" => $insertdata[':IP'], ":UID" => $insertdata[':UID']));
        } catch (PDOException $Exception) {
            error($Exception);
        }
        pjson("done");
    }


    pjson($data);
}

error("Sorry, path does not exist.");
