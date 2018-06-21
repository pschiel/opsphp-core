<?php

/**
 * DB class.
 *
 * Provides mysqli database functionality
 */
class DB {
	
	/** @var mysqli mysqli connection object */
	public $mysqli = null;

	/** @var array active instances */
	public static $instances = [];

	/**
	 * Get DB instance.
	 *
	 * @param string $dbconfig_key database config (if empty, use default)
	 * @return DB reference to DB instance
	 */
	public static function &getInstance($dbconfig_key = '') {

		if (!$dbconfig_key) {
			$dbconfig_key = DBConfig::$default;
		}
		if (isset(self::$instances[$dbconfig_key])) {
			$instance =& self::$instances[$dbconfig_key];
		} else {
			$instance = new DB();
			$instance->init(DBConfig::get($dbconfig_key));
			self::$instances[$dbconfig_key] =& $instance;
		}
		return $instance;

	}

	/**
	 * Initializes database connection.
	 */
	public function init($dbconfig) {

		mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);
		$mysqli = new mysqli($dbconfig['host'], $dbconfig['user'], $dbconfig['password'], $dbconfig['database'], 3306);
		$mysqli->set_charset('utf8');
		$this->mysqli = $mysqli;

	}

	/**
	 * Performs a SQL query and returns result for selects, insert id for inserts, affected rows else.
	 *
	 * Parameters will be escaped
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return mysqli_result|int mysqli result object, insert id or affected rows
	 */
	public function query($query, $params = null) {

		if ($params) {
			$this->escape($params);
			$query = vsprintf(str_replace('?', '%s', $query), $params);
		}
		if (DEBUG && SQLLOG) {
			$handle = fopen(SQLLOG, 'a');
			fwrite($handle, '[' . date('Y-m-d H:i') . '] ' . trim($query) . "\n");
			fclose($handle);
		}
		$result = $this->mysqli->query($query);
		$command = substr(ltrim(strtolower($query)), 0, 6);
		if ($command == 'select') {
			return $result;
		} elseif ($command == 'insert') {
			return $this->mysqli->insert_id;
		} else {
			return $this->mysqli->affected_rows;
		}

	}

	/**
	 * Escapes parameters for SQL query.
	 *
	 * @param array $params parameters
	 */
	public function escape(&$params) {

		foreach ($params as $key => $param) {
			if (is_array($param)) {
				$this->escape($param);
				$params[$key] = '(' . implode(',', $param) . ')';
			} else if ($param === null) {
				$params[$key] = 'NULL';
			} else if ($param === false) {
				$params[$key] = "0";
			} else if ($param === true) {
				$params[$key] = "1";
			} else {
				$params[$key] = "'" . $this->mysqli->real_escape_string($param) . "'";
			}
		}

	}

	/**
	 * Returns table and field names, mapped to field indexes
	 *
	 * @param mysqli_result $result mysqli result object
	 * @return array table and field names
	 */
	public function fieldmappings($result) {

		$mappings = [];
		$fields = $result->fetch_fields();
		foreach ($fields as $field) {
			$dotpos = strpos($field->name, '.');
			if ($dotpos === false && empty($field->table)) {
				$mappings[] = ['table' => 0, 'field' => $field->name];
			} elseif ($dotpos !== false) {
				$mappings[] = ['table' => substr($field->name, 0, $dotpos), 'field' => substr($field->name, $dotpos+1)];
			} else {
				$mappings[] = ['table' => $field->table, 'field' => $field->name];
			}
		}
		return $mappings;

	}

	/**
	 * Returns all result rows for a select
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @param string $index_field field used for array keys
	 * @return array result rows
	 */
	public function findAll($query, $params = null, $index_field = null) {

		$result = $this->query($query, $params);
		$mappings = $this->fieldmappings($result);
		$index = 0;
		if ($index_field) {
			foreach ($mappings as $i => $mapping) {
				if ($index_field == $mapping['table'] . '.' . $mapping['field']) {
					$index = $i;
				}
			}
		}
		$rows = [];
		while ($row = $result->fetch_row()) {
			$assoc = [];
			foreach ($mappings as $i => $mapping) {
				$assoc[$mapping['table']][$mapping['field']] = $row[$i];
			}
			if ($index_field) {
				$rows[$row[$index]] = $assoc;
			} else {
				$rows[] = $assoc;
			}
		}
		return $rows;

	}

	/**
	 * Returns generator for all result rows for a select
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return generator
	 */
	public function findAllGenerator($query, $params = null) {

		$result = $this->query($query, $params);
		$mappings = $this->fieldmappings($result);
		while ($row = $result->fetch_row()) {
			$assoc = [];
			foreach ($mappings as $i => $mapping) {
				$assoc[$mapping['table']][$mapping['field']] = $row[$i];
			}
			yield $assoc;
		}

	}

	/**
	 * Returns first result row for a query
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return array first result row
	 */
	public function findFirst($query, $params = null) {

		$rows = $this->findAll($query, $params);
		if (empty($rows)) {
			return [];
		}
		return $rows[0];

	}

	/**
	 * Returns values of first column for a query
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return array values
	 */
	public function findValues($query, $params = null) {

		$result = $this->query($query, $params);
		$values = [];
		while ($row = $result->fetch_row()) {
			$values[] = $row[0];
		}
		return $values;

	}

	/**
	 * Returns value of first result row for a query
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return mixed value of first result
	 */
	public function findValue($query, $params = null) {

		$values = $this->findValues($query, $params);
		return isset($values[0]) ? $values[0] : null;

	}

	/**
	 * Provides an iterator for all result rows
	 *
	 * @param string $query the SQL query
	 * @param array $params parameters (replaced into '?' in the query)
	 * @return Generator iterator function
	 */
	public function iterator($query, $params = null) {

		$result = $this->query($query, $params);
		while ($row = $result->fetch_assoc()) {
			yield $row;
		}

	}

	/**
	 * SQL begin transaction.
	 */
	public function begin() {

		$this->mysqli->query('begin');

	}

	/**
	 * SQL commit transaction.
	 */
	public function commit() {

		$this->mysqli->query('commit');

	}

	/**
	 * SQL rollback transaction.
	 */
	public function rollback() {

		$this->mysqli->query('rollback');

	}

}
