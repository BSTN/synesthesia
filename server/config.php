<?php

define('PRODUCTION', getenv('development') !== "true");
define('BASE', '/');
define('CONFIGBASE', '/synesthesia_config');
define('CONFIGPATH', './synesthesia_config');
define('SRCV', PRODUCTION ? '/js/vendors.js' : 'http://localhost:2222/vendors.main.dev.js');
define('SRC', PRODUCTION ? '/js/main.js' : 'http://localhost:2222/main.dev.js');
define('TEMP_PATH', '/tmp');
define('POSTGRES_USER', getenv('POSTGRES_USER'));
define('POSTGRES_PASSWORD', getenv('POSTGRES_PASSWORD'));
define('POSTGRES_DB', getenv('POSTGRES_DB'));
define('POSTGRES_HOST', getenv('POSTGRES_HOST'));
define('POSTGRES_PORT', getenv('POSTGRES_PORT'));
define('PASS', getenv('PASS'));
define('DB_PREFIX', getenv('DB_PREFIX', ""));
