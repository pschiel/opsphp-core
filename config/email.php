<?php

/**
 * Class EmailSMTPConfig
 */
class EmailSMTPConfig {

	/** @var string default email config */
	public static $default = 'default';
	
	public static $configs = [
		'default' => [
			'from' => 'foo@bar.net',
			'fromName' => 'Foo Bar',
			'smtpHost' => 'localhost',
			'smtpPort' => 25,
			'smtpUsername' => '',
			'smtpPassword' => '',
			'smtpTimeout' => 30
		]
	];
	
	/**
	 * Get email config.
	 *
	 * @param string $key email config
	 * @return array email config
	 * @throws Exception
	 */
	public static function get($key) {

		if (!isset(self::$configs[$key])) {
			throw new Exception('Unknown email config: ' . $key);
		}
		return self::$configs[$key];

	}
}
