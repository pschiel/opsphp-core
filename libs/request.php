<?php

/**
 * Request class
 */
class Request {

	/** @var string controller */
	public $controller = '';

	/** @var string action */
	public $action = 'index';

	/** @var array additional parameters for the action */
	public $params = [];


	/**
	 * Request constructor.
	 *
	 * Parses segments of the URL and GET/POST data
	 *
	 * @param string $url if this param is passed, use it instead of REQUEST_URI
	 */
	public function __construct($url = null) {

		if (is_null($url)) {
			$url = $_SERVER['REQUEST_URI'];
		}
		$url = trim($url, '/');
		$pos = strpos($url, '?');
		if ($pos !== false) {
			$url = substr($url, 0, $pos);
		}
		if ($url == '') {
			$url = trim(APP_HOME, '/');
		}
		$params = explode('/', $url);
		$param_count = count($params);
		if ($param_count > 0) {
			$this->controller = array_shift($params);
			if ($param_count > 1) {
				$this->action = array_shift($params);
				$this->params = $params;
			}
		}

	}

	/**
	 * Returns value of a GET or POST variable.
	 *
	 * Example: gpvar('User')              - returns User array from $_GET or $_POST
	 *          gpvar('Customer[message]') - returns $_GET[Customer][message] (or $_POST)
	 *
	 * @param string $key name of the variable
	 * @param string $default default value, if variable not set
	 * @return string value of the variable or null if not set
	 */
	public static function gpvar($key, $default = null) {

		$subkeys = explode('[', str_replace(']', '', $key));
		$element = &$_GET;
		if (isset($_POST[$subkeys[0]])) {
			$element = &$_POST;
		}
		foreach ($subkeys as $subkey) {
			if (isset($element[$subkey])) {
				$element = &$element[$subkey];
			} else {
				return $default;
			}
		}
		return $element;

	}

	/**
	 * Checks for ajax request.
	 *
	 * @return bool true, if request was an ajax call
	 */
	public static function isAjax() {

		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

	}

	/**
	 * Checks for JSON request.
	 *
	 * @return bool true, if request expects a JSON response
	 */
	public static function isJson() {

		return isset($_SERVER['HTTP_ACCEPT']) && strtolower(substr($_SERVER['HTTP_ACCEPT'], 0, 16)) === 'application/json';

	}

	/**
	 * Checks for POST request.
	 *
	 * @return bool true, if request was a POST
	 */
	public static function isPost() {

		return $_SERVER['REQUEST_METHOD'] === 'POST';

	}

	/**
	 * Creates a full URL for a given path.
	 *
	 * @param string $path path without protocol and host
	 * @return string full URL
	 */
	public static function fullUrl($path) {

		$url = 'http';
		if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
			$url .= 's';
		}
		$url .= '://' . $_SERVER['HTTP_HOST'] . $path;
		return $url;

	}

    /**
     * Returns url before rewrite, without get params
     *
     * @return string url
     * @throws Exception
     */
	public static function url() {

        // ninx rewrite
	    if (isset($_REQUEST['url'])) {
            return $_REQUEST['url'];
        }

        // apache rewrite
        elseif (isset($_SERVER['REQUEST_URI'])) {
            return preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
        }

        throw new Exception('could not get request url');

    }

}
