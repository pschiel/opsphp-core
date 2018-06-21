<?php

/**
 * Cache functions.
 */
class Cache {

	/**
	 * Retreives a value from the cache.
	 *
	 * @param string $key key of the value
	 * @return mixed value or null, if not found in cache or outdated
	 */
	public static function read($key) {

		$cached_data = apc_fetch($key);
		if (!$cached_data) {
			return null;
		}
		if ($cached_data['valid_to'] < time()) {
			return null;
		}
		return $cached_data['value'];

	}

	/**
	 * Stores a value in the cache.
	 *
	 * @param string $key key of the value
	 * @param mixed $value value to be stored
	 * @param int $timeout value becomes invalid after specified number of seconds
	 */
	public static function write($key, $value, $timeout = 0) {

		if (!$timeout) {
			$timeout = 365*24*60*60;
		}
		apc_store($key, array(
			'valid_to' => time() + $timeout,
			'value' => $value
		));

	}

	/**
	 * Deletes a value from the cache.
	 *
	 * @param string $key key of the value
	 */
	public static function delete($key) {

		apc_delete($key);

	}

}
