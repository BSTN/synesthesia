<?php

$PATH = getPath();

function getPath(){
  global $BASE;
  $req = $_SERVER['REQUEST_URI'];
  $currentlocation = rtrim($BASE,"/") . "/api";
  $url = preg_replace("/".preg_quote($currentlocation,"/")."/", "", $req);
  $url = parse_url($url, PHP_URL_PATH);
  $url = rtrim($url,"/");
  $url = strtolower($url);
  return $url;
}

// send json
function pjson($data) {
  header('Content-Type: application/json');
  echo json_encode($data);
  exit();
}

// error handling
function error($message, $e = true) {
  $return = array();
  $return['message'] = $message;
  $return['error'] = $e;
  $status_header = 'HTTP/1.1 400';
  header($status_header);
  pjson($return);
}