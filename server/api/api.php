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

  // the data object
  $data = (array) $postdata['data'];
  
  // prepare insert data
  $insertdata = array();
  foreach($data as $k => $v) { 
    $insertdata[":$k"] = $v; 
  }
  $insertdata[':IP'] = get_ip_address();
  $insertdata[':UID'] = $postdata['UID'];

  if ($postdata['table'] === 'profile') {

    // upsert?
    $prep = $dbc->prepare("
      INSERT INTO profile SET 
        IP=SHA2(:IP,256), 
        language=:language, 
        UID=:UID
      ON DUPLICATE KEY UPDATE
        IP=SHA2(:IP,256), 
        language=:language, 
        UID=:UID
    ;");
    $prep->execute($insertdata);

  } elseif ($postdata['table'] === 'questions') {
    $prep = $dbc->prepare("
      INSERT INTO profile SET 
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
      interrupted=:interrupted,
    ;");
    $prep->execute($insertdata);

  } elseif ($postdata['table'] === 'extraquestions') {
    $values = $insertdata[':values'];
    unset($insertdata[':values']);
    $prep = $dbc->prepare("
      SET @current := (SELECT data FROM extraquestions WHERE UID=:UID);
      INSERT INTO extraquestions SET 
        IP=SHA2(:IP,256), 
        UID=:UID,
        data=JSON_SET('{}', :key, :value)
      ON DUPLICATE KEY UPDATE
        IP=SHA2(:IP,256),
        data=JSON_SET(@current, :key, :value)
    ;");
    foreach($values as $k => $v) {
      $n = $insertdata;
      $n[":key"] = "$." . $k;
      $n[":value"] = $v;
      $prep->execute($n);
    }

  }
  

  pjson($data);
}

pjson(array("error_message" => "Path does not exist."));