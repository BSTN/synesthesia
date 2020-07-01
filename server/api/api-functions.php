<?php

$PATH = getPath();

require_once "export.php";

function getPath()
{
    global $BASE;
    $req = $_SERVER['REQUEST_URI'];
    $currentlocation = rtrim(BASE, "/") . "/api";
    $url = preg_replace("/" . preg_quote($currentlocation, "/") . "/", "", $req);
    $url = parse_url($url, PHP_URL_PATH);
    $url = rtrim($url, "/");
    $url = strtolower($url);
    return $url;
}

// send json
function pjson($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// error handling
function error($message, $e = false)
{
    error_log($message);
    $return = array();
    $return['message'] = $message;
    $return['error'] = $e;
    $status_header = 'HTTP/1.1 400';
    header($status_header);
    echo $message;
    if ($e) {
        echo "\n---------------\n";
        print_r($e);
    }
    exit();
}

// uniqe id
function getUid()
{
    $length = 64;
    // https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

// shared string
function getShared()
{
    $length = 32;
    // https://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

// ip
function get_ip_address()
{
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                // trim for safety measures
                $ip = trim($ip);
                // attempt to validate IP
                if (validate_ip($ip)) {
                    return $ip;
                }
            }
        }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
}

function validate_ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

function unwrap($text)
{
    return preg_replace("/<p><(.[\s\S]*?)><\/p>/", "<$1>", $text);
}

function joinPaths()
{
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array) $arg);
    }
    foreach ($paths as $k => $p) {
        if ($k === 0) {
            $paths[$k] = rtrim($p, "/");
        } else {
            $paths[$k] = trim($p, "/");
        }
    }
    $paths = array_filter($paths);
    return join('/', $paths);
}

function brute_check()
{
    global $dbc;
    $ip = get_ip_address();
    $table = DB_PREFIX . "access";
    $prep = $dbc->prepare(
        "SELECT * FROM $table 
        WHERE IP = ? 
        AND NUM > 9
        AND modified > NOW() - INTERVAL 10 MINUTE;"
    );
    try {
        $prep->execute(array($ip));
        $res = $prep->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $Exception) {
        error($Exception);
    }
    // $res = true (1) is found, false is not found: continue.
    return $res;
    exit();
}

function brute_fail()
{
    global $dbc;
    $ip = get_ip_address();
    $table = DB_PREFIX . "access";
    $prep = $dbc->prepare(
        "INSERT INTO $table SET
            IP=:IP,
            NUM=1
        ON DUPLICATE KEY UPDATE 
            NUM=MOD(NUM+1,11);"
    );
    try {
        $prep->execute(array(":IP" => $ip));
    } catch (PDOException $Exception) {
        error($Exception);
    }
}

function brute_reset()
{
    global $dbc;
    $ip = get_ip_address();
    $table = DB_PREFIX . "access";
    $prep = $dbc->prepare(
        "DELETE FROM $table WHERE IP=?;"
    );
    try {
        $prep->execute(array($ip));
    } catch (PDOException $Exception) {
        error($Exception);
    }
}
