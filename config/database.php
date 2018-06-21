<?php

/**
 * Class DBConfig
 */
class DBConfig {

	/** @var string default database config */
	public static $default = 'default';

	public static $configs = [
		'default' => [
			'testing' => [
				'host' => '1.2.3.4',
				'user' => 'user',
				'password' => 'pass',
				'database' => 'dbname'
			],
			'local' => [
				'host' => '127.0.0.1',
				'user' => 'user',
				'password' => 'pass',
				'database' => 'dbname'
			],
			'live' => [
				'host' => '1.2.3.4',
				'user' => 'user',
				'password' => 'pass',
				'database' => 'dbname'
			]
		]
	];

	/**
	 * Get database config.
	 *
	 * @param string $key database config
	 * @return array database config
	 * @throws Exception
	 */
	public static function get($key) {

		if (!isset(self::$configs[$key])) {
			throw new Exception('Unknown database config: ' . $key);
		}
		$subkey = TESTING ? (TESTINGLOCAL ? 'local' : 'testing') : 'live';
		$config = self::$configs[$key][$subkey];
		return $config;

	}

}
