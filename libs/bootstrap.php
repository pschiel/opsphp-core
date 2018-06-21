<?php

// include all necessary classes
require_once 'config/core.php';
require_once 'config/database.php';
require_once 'libs/app.php';
require_once 'libs/request.php';
require_once 'libs/controller.php';
require_once 'libs/component.php';
require_once 'libs/model.php';
require_once 'libs/view.php';
require_once 'libs/helper.php';
require_once 'libs/db.php';
require_once 'libs/session.php';
require_once 'libs/cache.php';
require_once 'controllers/app_controller.php';
require_once 'models/app_model.php';
foreach (glob('interfaces/*.php') as $filename) {
	include $filename;
}
if (@file_exists('vendor/autoload.php')) {
    require_once 'vendor/autoload.php';
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
