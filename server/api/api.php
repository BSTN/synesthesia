<?php

// dependencies
include_once("../config.php");
include_once("api-functions.php");

// connect to database 
try {
  $dbc = new PDO('mysql:host='.$MYSQL_HOST.';dbname='.$MYSQL_DBNAME, $MYSQL_USERNAME, $MYSQL_PASSWORD . '???');
  $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  error("Could not connect to database.");
}

// api
if($PATH === "/test") {
  pjson(array("hello" => "basten"));
};


pjson(array("error_message" => "Path does not exist."));

?>