<?php

function export_to_csv()
{
  global $dbc;

  // create dir/files if not exists
  $profilePath = joinPaths(TEMP_PATH, "/profiles—" . date("Y.m.d—H.i.s") . ".csv");
  $questionsPath = joinPaths(TEMP_PATH, "/questions—" . date("Y.m.d—H.i.s") . ".csv");
  if (!file_exists(TEMP_PATH)) {
    mkdir(TEMP_PATH, 0755, true);
  }

  // first collect all extra columns (from extra data JSON)
  $extraColumns = get_extra_columns();

  // load all profile columns
  $profileColumns = get_profile_columns();

  // merge profile columns with extracolumns
  $allProfileColumns = array_merge($profileColumns, $extraColumns);

  // file handle
  $profileFile = fopen($profilePath, 'w');

  // write column names
  fputcsv($profileFile, $allProfileColumns);

  // export profile data to csv file
  $profiletable = DB_PREFIX . "profile";
  $extratable = DB_PREFIX . "extra";
  $query = "SELECT $profiletable.created as created, 
            $extratable.modified as modified, 
            $profiletable.UID as UID, 
            $profiletable.IP as IP, 
            $profiletable.language as language, 
            $profiletable.USERID as USERID, 
            $profiletable.SHARED as SHARED, 
            $extratable.data as data 
            FROM $profiletable INNER JOIN $extratable 
            ON $profiletable.UID = $extratable.UID;";
  $res = $dbc->query($query);
  while ($row = $res->fetch(PDO::FETCH_NAMED)) {
    $array = [];
    foreach ($row as $name => $value) {
      if ($name === 'data') {
        $JSON = (array) json_decode($value);
        foreach ($extraColumns as $c) {
          if (isset($JSON, $c)) {
            $array[] = $JSON[$c];
          } else {
            $array[] = "";
          };
        };
      } else {
        $array[] = $value;
      }
    }
    fputcsv($profileFile, $array);
  }
  fclose($profileFile);


  // fetch questions
  $questionsColumns = get_questions_columns();

  // file handle
  $questionsFile = fopen($questionsPath, 'w');

  // write column names
  fputcsv($questionsFile, $questionsColumns);

  // fetch and write data
  $query = "SELECT * FROM " . DB_PREFIX . "questions;";
  $res = $dbc->query($query);
  while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($questionsFile, array_values($row));
  }
  fclose($questionsFile);

  // zip both files and download

  $files = array($profilePath, $questionsPath);
  $zipPath = joinPaths(TEMP_PATH, 'bundled-' . date("Y.m.d—H.i.s") . ".zip");
  $zip = new ZipArchive;
  $zip->open($zipPath, ZipArchive::CREATE);
  foreach ($files as $file) {
    $zip->addFile($file, basename($file));
  }
  $zip->close();

  header('Content-Type: application/zip');
  header('Content-disposition: attachment; filename=' . 'synesthesia-data-' . date("Y.m.d—H.i.s") . ".zip");
  header('Content-Length: ' . filesize($zipPath));
  readfile($zipPath);

  // remove files from temporary folder

  unlink($profilePath);
  unlink($questionsPath);
  unlink($zipPath);

  exit();
}


function get_extra_columns()
{
  global $dbc;
  $table = DB_PREFIX . "extra";
  $prep = $dbc->prepare("SELECT data FROM $table");
  try {
    $prep->execute();
  } catch (PDOException $Exception) {
    error($Exception);
  }
  $res = $prep->fetchAll(PDO::FETCH_COLUMN, 'data');
  // echo "<pre>" . print_r($res, true) . "</pre>";
  $extras = array();
  foreach ($res as $v) {
    $JSON = json_decode($v);
    if ($JSON) {
      foreach ($JSON as $K => $value) {
        $extras[$K] = $K;
      }
    }
  }
  $extras = array_keys($extras);
  // echo "<pre>" . print_r($extras, true) . "</pre>";
  return $extras;
}

function get_profile_columns()
{
  global $dbc;
  $table = DB_PREFIX . "profile";
  $prep = $dbc->prepare("SHOW COLUMNS FROM $table;");
  try {
    $prep->execute();
  } catch (PDOException $Exception) {
    error($Exception);
  }
  $columns = $prep->fetchAll(PDO::FETCH_COLUMN);
  return $columns;
}

function get_questions_columns()
{
  global $dbc;
  $table = DB_PREFIX . "questions";
  $prep = $dbc->prepare("SHOW COLUMNS FROM $table;");
  try {
    $prep->execute();
  } catch (PDOException $Exception) {
    error($Exception);
  }
  $columns = $prep->fetchAll(PDO::FETCH_COLUMN);
  return $columns;
}
