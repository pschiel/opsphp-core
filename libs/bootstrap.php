<?php

// include all necessary classes
require APPDIR . '/config/core.php';
require APPDIR . '/config/database.php';
require COREDIR . '/libs/app.php';
require COREDIR . '/libs/request.php';
require COREDIR . '/libs/controller.php';
require COREDIR . '/libs/component.php';
require COREDIR . '/libs/model.php';
require COREDIR . '/libs/view.php';
require COREDIR . '/libs/helper.php';
require COREDIR . '/libs/db.php';
require COREDIR . '/libs/session.php';
require COREDIR . '/libs/cache.php';
require APPDIR . '/controllers/app_controller.php';
require APPDIR . '/models/app_model.php';
foreach (glob(APPDIR . '/interfaces/*.php') as $filename) {
	include $filename;
}
require APPDIR . '/vendor/autoload.php';

// error handling
ini_set('display_errors', DEBUG ? 1 : 0);
ini_set('error_reporting', E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', ERRORLOG);
ini_set('log_errors_max_len', 150000);

// set utf8
if (ini_get('default_charset')) {
	ini_set('default_charset', 'UTF-8');
} else {
	ini_set('mbstring.internal_encoding', 'UTF-8');
}
