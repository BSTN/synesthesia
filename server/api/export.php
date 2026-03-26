<?php

function get_extra_columns()
{
    $dbc = db();
    $table = db_table("extra");
    $prep = $dbc->prepare("SELECT data FROM $table");
    $prep->execute();
    $res = $prep->fetchAll(PDO::FETCH_COLUMN, 0);
    $extras = array();
    foreach ($res as $v) {
        $json = json_decode($v, true);
        if (is_array($json)) {
            foreach ($json as $key => $value) {
                $extras[$key] = $key;
            }
        }
    }
    return array_keys($extras);
}

function get_profile_columns()
{
    return db_table_columns(db(), db_table("profile"));
}

function get_questions_columns()
{
    return db_table_columns(db(), db_table("questions"));
}
