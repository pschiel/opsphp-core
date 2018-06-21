<?php

// include all necessary classes
require_once APPDIR . '/config/core.php';
require_once APPDIR . '/config/database.php';
require_once COREDIR . '/libs/app.php';
require_once COREDIR . '/libs/request.php';
require_once COREDIR . '/libs/controller.php';
require_once COREDIR . '/libs/component.php';
require_once COREDIR . '/libs/model.php';
require_once COREDIR . '/libs/view.php';
require_once COREDIR . '/libs/helper.php';
require_once COREDIR . '/libs/db.php';
require_once COREDIR . '/libs/session.php';
require_once COREDIR . '/libs/cache.php';
require_once APPDIR . '/controllers/app_controller.php';
require_once APPDIR . '/models/app_model.php';
foreach (glob(APPDIR . '/interfaces/*.php') as $filename) {
	include $filename;
}
if (@file_exists(APPDIR . '/vendor/autoload.php')) {
    include APPDIR . '/vendor/autoload.php';
}

// error handling
ini_set('display_errors', DEBUG ? 1 : 0);
ini_set('error_reporting', E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', ERRORLOG);
ini_set('log_errors_max_len', 150000);

// utf8, disable browser cache
if (ini_get('default_charset')) {
	ini_set('default_charset', 'UTF-8');
} else {
	ini_set('mbstring.internal_encoding', 'UTF-8');
}
