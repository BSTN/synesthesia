<?php

$PATH = getPath();

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
function error($message, $e = true)
{
    error_log($message);
    $return = array();
    $return['message'] = $message;
    $return['error'] = $e;
    $status_header = 'HTTP/1.1 400';
    header($status_header);
    pjson($return);
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
