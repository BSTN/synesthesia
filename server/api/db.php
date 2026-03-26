<?php

function db()
{
    static $dbc = null;

    if ($dbc instanceof PDO) {
        return $dbc;
    }

    ensure_directory(dirname(SQLITE_PATH));
    $dbc = new PDO('sqlite:' . SQLITE_PATH);
    $dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbc->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbc->exec('PRAGMA foreign_keys = ON;');
    $dbc->exec('PRAGMA journal_mode = WAL;');

    return $dbc;
}

function db_now()
{
    return gmdate('Y-m-d H:i:s');
}

function db_hash_ip($ip)
{
    if (!$ip) {
        return null;
    }

    return hash('sha256', $ip);
}

function db_table($name)
{
    return DB_PREFIX . $name;
}

function db_setup_schema(PDO $dbc)
{
    $profile = db_table('profile');
    $questions = db_table('questions');
    $extra = db_table('extra');
    $access = db_table('access');
    $meta = db_table('app_meta');

    $statements = array(
        "CREATE TABLE IF NOT EXISTS $profile (
            UID TEXT PRIMARY KEY,
            created TEXT NOT NULL,
            modified TEXT NOT NULL,
            IP TEXT,
            language TEXT,
            finishedtests TEXT,
            touchscreen TEXT,
            USERID TEXT,
            SHARED TEXT
        )",
        "CREATE TABLE IF NOT EXISTS $questions (
            ID INTEGER PRIMARY KEY AUTOINCREMENT,
            created TEXT NOT NULL,
            modified TEXT NOT NULL,
            IP TEXT,
            UID TEXT NOT NULL,
            testname TEXT,
            setname TEXT,
            symbol TEXT,
            value TEXT,
            clicks INTEGER,
            clicksslider INTEGER,
            position TEXT,
            timing INTEGER,
            qnr INTEGER
        )",
        "CREATE TABLE IF NOT EXISTS $extra (
            UID TEXT PRIMARY KEY,
            created TEXT NOT NULL,
            modified TEXT NOT NULL,
            IP TEXT,
            data TEXT
        )",
        "CREATE TABLE IF NOT EXISTS $access (
            IP TEXT PRIMARY KEY,
            created TEXT NOT NULL,
            modified TEXT NOT NULL,
            NUM INTEGER NOT NULL DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS $meta (
            meta_key TEXT PRIMARY KEY,
            meta_value TEXT NOT NULL
        )",
        "CREATE INDEX IF NOT EXISTS idx_{$questions}_uid ON $questions (UID)",
        "CREATE INDEX IF NOT EXISTS idx_{$questions}_testname ON $questions (testname)",
        "CREATE INDEX IF NOT EXISTS idx_{$profile}_shared ON $profile (SHARED)",
    );

    foreach ($statements as $statement) {
        $dbc->exec($statement);
    }
}

function db_upsert_profile(PDO $dbc, array $data)
{
    $table = db_table('profile');
    $stmt = $dbc->prepare(
        "INSERT INTO $table (UID, created, modified, IP, language, finishedtests, touchscreen, USERID, SHARED)
        VALUES (:UID, :created, :modified, :IP, :language, :finishedtests, :touchscreen, :USERID, :SHARED)
        ON CONFLICT(UID) DO UPDATE SET
            modified = excluded.modified,
            IP = excluded.IP,
            language = excluded.language,
            finishedtests = excluded.finishedtests,
            touchscreen = excluded.touchscreen,
            USERID = COALESCE(excluded.USERID, $table.USERID),
            SHARED = COALESCE(excluded.SHARED, $table.SHARED)"
    );

    $stmt->execute(array(
        ':UID' => $data['UID'],
        ':created' => $data['created'],
        ':modified' => $data['modified'],
        ':IP' => $data['IP'],
        ':language' => $data['language'] ?? null,
        ':finishedtests' => $data['finishedtests'] ?? null,
        ':touchscreen' => $data['touchscreen'] ?? null,
        ':USERID' => $data['USERID'] ?? null,
        ':SHARED' => $data['SHARED'] ?? null,
    ));
}

function db_insert_question(PDO $dbc, array $data)
{
    $table = db_table('questions');
    $stmt = $dbc->prepare(
        "INSERT INTO $table (created, modified, IP, UID, testname, setname, symbol, value, clicks, clicksslider, position, timing, qnr)
        VALUES (:created, :modified, :IP, :UID, :testname, :setname, :symbol, :value, :clicks, :clicksslider, :position, :timing, :qnr)"
    );

    $stmt->execute(array(
        ':created' => $data['created'],
        ':modified' => $data['modified'],
        ':IP' => $data['IP'],
        ':UID' => $data['UID'],
        ':testname' => $data['testname'] ?? null,
        ':setname' => $data['setname'] ?? null,
        ':symbol' => $data['symbol'] ?? null,
        ':value' => $data['value'] ?? null,
        ':clicks' => $data['clicks'] ?? null,
        ':clicksslider' => $data['clicksslider'] ?? null,
        ':position' => $data['position'] ?? null,
        ':timing' => $data['timing'] ?? null,
        ':qnr' => $data['qnr'] ?? null,
    ));
}

function db_upsert_extra(PDO $dbc, array $data)
{
    $table = db_table('extra');
    $stmt = $dbc->prepare(
        "INSERT INTO $table (UID, created, modified, IP, data)
        VALUES (:UID, :created, :modified, :IP, :data)
        ON CONFLICT(UID) DO UPDATE SET
            modified = excluded.modified,
            IP = excluded.IP,
            data = excluded.data"
    );

    $stmt->execute(array(
        ':UID' => $data['UID'],
        ':created' => $data['created'],
        ':modified' => $data['modified'],
        ':IP' => $data['IP'],
        ':data' => $data['data'],
    ));
}

function db_existing_extra(PDO $dbc, $uid)
{
    $table = db_table('extra');
    $stmt = $dbc->prepare("SELECT data FROM $table WHERE UID = :UID");
    $stmt->execute(array(':UID' => $uid));
    $existing = $stmt->fetchColumn();

    if (!$existing) {
        return array();
    }

    $decoded = json_decode($existing, true);
    return is_array($decoded) ? $decoded : array();
}

function db_table_columns(PDO $dbc, $table)
{
    $stmt = $dbc->query("PRAGMA table_info($table)");
    $columns = array();

    foreach ($stmt->fetchAll() as $column) {
        $columns[] = $column['name'];
    }

    return $columns;
}

function db_get_meta(PDO $dbc, $key, $default = null)
{
    $table = db_table('app_meta');
    $stmt = $dbc->prepare("SELECT meta_value FROM $table WHERE meta_key = :meta_key");
    $stmt->execute(array(':meta_key' => $key));
    $value = $stmt->fetchColumn();
    return $value === false ? $default : $value;
}

function db_set_meta(PDO $dbc, $key, $value)
{
    $table = db_table('app_meta');
    $stmt = $dbc->prepare(
        "INSERT INTO $table (meta_key, meta_value)
        VALUES (:meta_key, :meta_value)
        ON CONFLICT(meta_key) DO UPDATE SET meta_value = excluded.meta_value"
    );
    $stmt->execute(array(':meta_key' => $key, ':meta_value' => (string) $value));
}

function db_mark_data_changed(PDO $dbc)
{
    db_set_meta($dbc, 'last_data_change_at', db_now());
}
