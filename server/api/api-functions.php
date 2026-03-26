<?php

$PATH = getPath();

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/export.php";
require_once __DIR__ . "/backup.php";
require_once __DIR__ . "/update.php";

function getPath()
{
    $req = $_SERVER['REQUEST_URI'];
    $currentlocation = rtrim(BASE, "/") . "/api";
    $url = preg_replace("/" . preg_quote($currentlocation, "/") . "/", "", $req);
    $url = parse_url($url, PHP_URL_PATH);
    $url = rtrim($url, "/");
    $url = strtolower($url);
    return $url;
}

function pjson($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

function error($message, $e = false)
{
    error_log((string) $message);
    header('HTTP/1.1 400');
    echo $message;
    if ($e) {
        echo "\n---------------\n";
        print_r($e);
    }
    exit();
}

function errormessage($message, $file)
{
    include_once($file);
    exit();
}

function getUid()
{
    $length = 64;
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

function getShared()
{
    $length = 32;
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

function get_ip_address()
{
    $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
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

function rrmdir($src)
{
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file !== '.') && ($file !== '..')) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                rrmdir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($src);
    return true;
}

function cleanUpQuery($string)
{
    $string = preg_replace("/[\r\n]+/", "\n", $string);
    $string = preg_replace("/\s+/", ' ', $string);
    return $string;
}
