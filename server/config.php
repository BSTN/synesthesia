<?php

define('PRODUCTION', getenv('development') !== "true");
define('BASE', '/');
define('CONFIGBASE', '/synesthesia_config');
define('CONFIGPATH', './synesthesia_config');
define('SRCV', PRODUCTION ? '/js/vendors.js' : 'http://localhost:2222/vendors.main.dev.js');
define('SRC', PRODUCTION ? '/js/main.js' : 'http://localhost:2222/main.dev.js');
define('TEMP_PATH', '/tmp');
define('MYSQL_USER', getenv('MYSQL_USER'));
define('MYSQL_PASSWORD', getenv('MYSQL_PASSWORD'));
define('MYSQL_DBNAME', getenv('MYSQL_DBNAME', "database"));
define('MYSQL_HOST', getenv('MYSQL_HOST', "localhost"));
define('MYSQL_PORT', getenv('MYSQL_PORT', "3306"));
define('PASS', getenv('PASS'));
define('DB_PREFIX', getenv('DB_PREFIX', ""));
