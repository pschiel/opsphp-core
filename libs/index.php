<?php

define('COREDIR', __DIR__);
define('APPDIR', dirname(dirname(dirname(dirname(__DIR__)))));
chdir(APPDIR);

// start app
try {

	require_once COREDIR . '/libs/bootstrap.php';

	// console call
	if (PHP_SAPI == 'cli') {
		if ($argc != 2) {
			throw new Exception('URL is missing');
		}
		new App($argv[1]);
	}

	// web call
	else {
		new App();
	}

}

// error handling
catch (Exception $e) {

	ob_end_clean();
	if ($e->getCode() && !TESTING) {
		header('HTTP/1.1 ' . $e->getCode());
	}

	$success = false;
	$error = $e->getMessage();

	echo json_encode(compact('success', 'error'));

}
