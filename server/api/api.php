<?php

// dependencies
include_once("../config.php");
include_once("api-functions.php");

// connect to database 
try {
  $dbc = new PDO('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
  $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  error("Could not connect to database.");
}

// api
if($PATH === "/test") {
  pjson(array("hello" => "basten"));
};

if($PATH === "/uid") {
  $data = array("uid" => getUid(64));
  pjson($data);
}

if($PATH === "/store") {

  /*
  UID: set uid
  data: data object
  table: which table to edit
  type: insert or update
  */
  
  // get json
  $input = file_get_contents('php://input');
  if (!$input) error("Missing Post data.");

  $postdata = (array) json_decode($input);

  if (!key_exists("UID", $postdata)) error("Missing UID.");
  if (!key_exists("data", $postdata)) error("Missing data.");
  if (!key_exists("table", $postdata)) error("Missing table.");
  if (!key_exists("type", $postdata)) error("Missing type.");

  // the data object
  $data = (array) $postdata['data'];
  
  // include IP
  $data['IP'] = get_ip_address();

  // prepare
  $insertdata = array();
  foreach($data as $k => $v) {
    $insertdata[":$k"] = $v;
  }
  $insertdata[':UID'] = $postdata['UID'];

  $prep = $dbc->prepare("
    INSERT INTO profile SET 
    IP=SHA2(:IP,256), 
    language=:language, 
    UID=:UID
  ;");
  $prep->execute($insertdata);

  pjson($data);
}

pjson(array("error_message" => "Path does not exist."));

?>