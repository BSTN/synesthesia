<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Michelf\Markdown;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

require_once "../config.php";

/*
 * Connect to database
 */
try {
    $dbc = new PDO(
        'mysql:host=' . MYSQL_HOST . ';
        port=' . MYSQL_PORT . ';
        dbname=' . MYSQL_DBNAME,
        MYSQL_USER,
        MYSQL_PASSWORD
    );
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Could not connect to database:\n $e";
    error_log("Could not connect to database:\n $e");
    exit();
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
    $sql = file_get_contents(dirname(__DIR__) . "/setup/setup.sql");
    $sql = preg_replace("/CREATE TABLE `/i", "CREATE TABLE `" . DB_PREFIX, $sql);
    try {
        $dbc->exec($sql);
    } catch (PDOException $Exception) {
        error($Exception);
    };
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
    if (!key_exists('USERID', $data)) {
        $data['USERID'] = null;
    };

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
            USERID=:USERID,
            SHARED=:SHARED"
    );
    $prep->execute($insertdata);

    pjson(array("UID" => $data['UID'], "SHARED" => $data['SHARED']));
}

if ($PATH === "/getshared") {
    /*
    * Retrieve shared data: post request
    *
    * @param code
    */
    $input = file_get_contents('php://input');
    if (!$input) {
        error("Missing Post data.");
    };
    $data = (array) json_decode($input);
    if (!key_exists('code', $data)) {
        error("Missing shared code.");
    };
    $code = $data['code'];

    /* get list of finished tests */
    $prep = $dbc->prepare(
        "SELECT finishedtests, UID FROM " . DB_PREFIX . "profile WHERE shared = :shared;"
    );
    try {
        $prep->execute(array("shared" => $code));
    } catch (PDOException $Exception) {
        error($Exception);
    }

    $fetch = $prep->fetch();
    if ($fetch === false) {
        pjson('No finished tests found.');
    }

    $finished = explode(",", $fetch['finishedtests']);
    $UID = $fetch['UID'];

    /* get test data */
    $results = array();
    // filter function
    function filter($v)
    {
        $newobj;
        foreach ($v as $k => $vv) {
            if (!in_array($k, ["IP","UID","USERID","ID","created","modified"])) {
                $newobj[$k] = $vv;
            }
        };
        return $newobj;
    };
    foreach ($finished as $testname) {
        $prep = $dbc->prepare(
            "SELECT * FROM " . DB_PREFIX . "questions WHERE testname = :testname AND UID = :UID;"
        );
        try {
            $prep->execute(array("testname" => $testname, "UID" => $UID));
        } catch (PDOException $Exception) {
            error($Exception);
        }
        $fetched = $prep->fetchAll(PDO::FETCH_CLASS);
        if (!$fetched) {
            $results[$testname] = false;
        } else {
            $filtered = array_map("filter", $fetched);
            $results[$testname] = $filtered;
        }
    }

    /* add extra question results (likert) */
    $prep = $dbc->prepare(
        "SELECT * FROM " . DB_PREFIX . "extra WHERE UID = :UID;"
    );
    try {
        $prep->execute(array("UID" => $UID));
    } catch (PDOException $Exception) {
        error($Exception);
    }
    $fetched = $prep->fetchAll();
    if (!$fetched) {
        $results['_extra'] = false;
    } else {
        $extra = json_decode($fetched[0]['data']);
        $results['_extra'] = $extra;
    }

    pjson($results);
}

if ($PATH === "/update") {
    exec('cd /var/www/html/synesthesia_config && git pull', $output, $retval);
    if ($retval === 0 && $output[0] === 'Already up to date.') {
        exec('cd /var/www/html/synesthesia_config/ && git branch --show-current', $output2);
        echo "Git branch \"" . $output2[0] . "\" is active and up to date.";
    } else {
        print_r($output);
    }
    exit();
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
        if (gettype($v) === 'object') {
            $v = json_encode($v);
        };
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

        $prep = $dbc->prepare(cleanUpQuery(
            "INSERT INTO " . DB_PREFIX . "profile SET 
                created=NOW(),
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID,
                finishedtests=:finishedtests,
                touchscreen=:touchscreen
            ON DUPLICATE KEY UPDATE
                IP=SHA2(:IP,256), 
                language=:language, 
                UID=:UID,
                finishedtests=:finishedtests,
                touchscreen=:touchscreen
            ;"
        ));
        try {
            $prep->execute($insertdata);
        } catch (PDOException $Exception) {
            error_log("Save profile data: " . print_r($insertdata, true));
            error($Exception);
        }
    } elseif ($postdata['table'] === 'questions') {
        $prep = $dbc->prepare(cleanUpQuery(
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
        ));

        try {
            $prep->execute($insertdata);
        } catch (PDOException $Exception) {
            error_log("Save data on error: " . print_r($insertdata, true));
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
            error_log("Save extra data on error: " . print_r($insertdata, true));
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

        $prep = $dbc->prepare(cleanUpQuery(
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
        ));

        try {
            $prep->execute(array(":data" => $data, ":IP" => $insertdata[':IP'], ":UID" => $insertdata[':UID']));
        } catch (PDOException $Exception) {
            error_log("Save extra data (JSON) on error: " . print_r($data, true));
            error($Exception);
        }
        pjson("done");
    }


    pjson($data);
}

error("Sorry, path does not exist.");
