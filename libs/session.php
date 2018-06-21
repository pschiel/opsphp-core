<?php

/**
 * Session handling.
 */
class Session {

	/**
	 * Starts the session.
	 *
	 * @throws Exception
	 */
	public static function start() {

		if (session_status() == PHP_SESSION_DISABLED) {
			throw new Exception('Sessions could not be started');
		}

		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

		return true;

	}

	/**
	 * Reads a value from the session.
	 *
	 * @param string $key key of the value
	 * @return mixed the value
	 */
	public static function read($key) {

		self::start();
		if (!isset($_SESSION[$key])) {
			return null;
		}
		return $_SESSION[$key];

	}

	/**
	 * Stores a value in the session.
	 *
	 * @param string $key key of the value
	 * @param mixed $value the value
	 */
	public static function write($key, $value) {

		self::start();
		$_SESSION[$key] = $value;

	}

	/**
	 * Deletes a value from the session.
	 *
	 * @param string $key key of the value
	 */
	public static function delete($key) {

		self::start();
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}

	}

	/**
	 * Ends up the cookie by setting lifetime to past.
	 *
	 * @param string $key key of the value
	 */
	public static function deleteCookie($key) {

		if (isset($_COOKIE[$key])) {
			$cookieInfo = session_get_cookie_params();
			if ((empty($cookieInfo['domain'])) && (empty($cookieInfo['secure']))) {
				setcookie($key, '', time() - 3600, $cookieInfo['path']);
			} elseif (empty($cookieInfo['secure'])) {
				setcookie($key, '', time() - 3600, $cookieInfo['path'], $cookieInfo['domain']);
			} else {
				setcookie($key, '', time() - 3600, $cookieInfo['path'], $cookieInfo['domain'], $cookieInfo['secure']);
			}
		}

	}

	/**
	 * Gets cookie
	 *
	 * @param string $key cookie key
	 * @return string cookie value
	 */
	public static function getCookie($key) {

		if (!isset($_COOKIE[$key])) {
			return '';
		}
		return $_COOKIE[$key];

	}

	/**
	 * Sets cookie
	 *
	 * @param string $key cookie key
	 * @param string $value cookie value
	 */
	public static function setCookie($key, $value) {

		setcookie($key, $value, 0, '/');

	}

	/**
	 * Deletes a value from the session.
	 *
	 * @since Version 1.1
	 */
	public static function destroy() {

		self::start();
		session_destroy();

	}

	/**
	 * Sets a flash message.
	 *
	 * @param string $message the flash message
	 * @param string $type alert type (info, success, warning, danger)
	 * @param string $key message key
	 */
	public static function setFlash($message, $type = 'info', $key = 'message') {

		self::start();
		self::write('flash-' . $key, ['message' => $message, 'type' => $type]);

	}

	/**
	 * Displays a flash message.
	 *
	 * @param string $key message key
	 */
	public static function flash($key = 'message') {

		self::start();
		if (isset($_SESSION['flash-' . $key])) {
			$flash = $_SESSION['flash-' . $key];
			$message = $flash['message'];
			$type = $flash['type'];
			unset($_SESSION['flash-' . $key]);
			echo '<div class="alert alert-' . $type . '" role="alert">' . $message . '</div>';
		}

	}

}
