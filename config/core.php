<?php

// home page controller/action
define('APP_HOME', '/pages');

// provide a hostname for CLI access
if (PHP_SAPI == 'cli') {
	$_SERVER['HTTP_HOST'] = 'foo.bar.net';
}

// domain/subdomain
define('TESTING', true);
define('TESTINGLOCAL', true);

// debug
define('DEBUG', TESTING ? 1 : 0);

// session settings
define('SESSION_NAME', 'api');
define('SESSION_PATH', APPDIR . '/tmp/sessions');
define('SESSION_DOMAIN', $_SERVER['HTTP_HOST']);
define('COOKIE_SECURE', false);

// paths
define('SQLLOG', APPDIR . '/tmp/logs/sql.log');
define('ERRORLOG', APPDIR . '/tmp/logs/error.log');
