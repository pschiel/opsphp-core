<?php

/**
 * Model class.
 */
class Model {

	/** @var string database config */
	public $dbconfig = '';

	/** @var DB database instance */
	public $db = null;

	/**
	 * Model constructor.
	 */
	public function __construct() {

		$this->db = DB::getInstance($this->dbconfig);

	}

	/**
	 * Validate data
	 *
	 * Format of validation rule array:
	 * [
	 *   'tablename' => [
	 *     'fieldname' => [
	 *        'rule' => 'rule value',
	 *        'rule' => ['rule_value' => 'custom error message'],
	 *        ....
	 *     ],
	 *     'fieldname' => ....
	 * 	 ],
	 *   'tablename' => ...
	 * ]
	 *
	 * Validation rules:
	 *  trim                  - trims input (need to be first rule if it exists)
	 *  striphtml             - strips html tags (tags can be specified by rule value)
	 *  stripUnprintableChars - stips unprintable characters
	 *  normalizeWhitespaces  - removes whitespaces if rule value true, otherwise normalize to \n
	 *  required              - input must exist and not be empty, if rule value is true
	 *  required_if_empty     - input must exist and not be empty, if field specified in rule value is empty
	 *  required_if_not_empty - input must exist and not be empty, if field specified in rule value is not empty
	 *  minlength             - input must have minimum length specified by rule value
	 *  maxlength             - input must have maximum length specified by rule value
	 *  minvalue              - input must have minimum value specified by rule value
	 *  maxvalue              - input must have maximum value specified by rule value
	 *  regexp                - input must match to given regexp
	 *  unique                - input must be unique in table, if rule value is true
	 *  exists                - input is an ID and must exist in the table specified by rule value
	 *  function              - custom validation function with parameters $data, $table and $field
	 *                          function name specified by rule value, must exist in current model or AppModel
	 *                          function returns error message (or empty string if validation is successful)
	 *
	 * @param array $data data
	 * @param array $validate_rules custom validation rules (if empty, use $validate_rules from model)
	 * @return array validation errors
	 * @throws Exception
	 */
	public function validate(&$data, $validate_rules = []) {

		if (empty($validate_rules) && !empty($this->validate_rules)) {
			$validate_rules = $this->validate_rules;
		}
		$errors = [];
		foreach ($validate_rules as $table => $fields) {
			foreach ($fields as $field => $rules) {
				foreach ($rules as $rule => $rule_value) {
					if (is_array($rule_value)) {
						$error = array_values($rule_value)[0];
						$rule_value = array_keys($rule_value)[0];
					} else {
						$error = null;
					}
					if ($rule == 'trim') {
						$data[$table][$field] = trim($data[$table][$field]);
					} elseif($rule == 'striphtml'){
						$data[$table][$field] = strip_tags($data[$table][$field], $rule_value);
					} elseif($rule == 'stripUnprintableChars'){
						$data[$table][$field] = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $data[$table][$field]);
					} elseif($rule == 'normalizeWhitespaces'){
						if( !empty($rule_value) ){
							$data[$table][$field] = preg_replace('/[\x0A\x0D]+/u', " ", $data[$table][$field]); // Remove newlines: LF,CR
						} else{
							$data[$table][$field] = preg_replace('/[\x0A\x0D]+/u', "\n", $data[$table][$field]); // Normalize newlines: LF,CR
						}
						$data[$table][$field] = preg_replace('/[\x{2028}\x{2029}\x0B\x20]+/u', " ", $data[$table][$field]); // Whitespaces are: LS, PS, VT, Space
						$data[$table][$field] = trim( $data[$table][$field], " ");
					} elseif ($rule == 'required' && $rule_value) {
						if (!isset($data[$table][$field]) || $data[$table][$field] == '') {
							$errors[$table][$field] = $error ? $error : 'Feld darf nicht leer sein';
							break;
						}
					} elseif ($rule == 'required_if_empty') {
						if (empty($data[$table][$field]) && empty($data[$table][$rule_value])) {
							$errors[$table][$field] = $error ? $error : 'Feld darf nicht leer sein';
							break;
						}
					} elseif ($rule == 'required_if_not_empty') {
						if (empty($data[$table][$field]) && !empty($data[$table][$rule_value])) {
							$errors[$table][$field] = $error ? $error : 'Feld darf nicht leer sein';
							break;
						}
					} elseif ($rule == 'minlength') {
						if (strlen($data[$table][$field]) < $rule_value) {
							$errors[$table][$field] =  $error ? $error : 'Eingabe zu kurz (mindestens ' . $rule_value . ' Zeichen)';
							break;
						}
					} elseif ($rule == 'maxlength') {
						if (strlen($data[$table][$field]) > $rule_value) {
							$errors[$table][$field] =  $error ? $error : 'Eingabe zu lang (höchstens ' . $rule_value . ' Zeichen)';
							break;
						}
					} elseif ($rule == 'minvalue') {
						if ($data[$table][$field] < $rule_value) {
							$errors[$table][$field] =  $error ? $error : 'Eingabe zu niedrig (mindestens ' . $rule_value . ')';
							break;
						}
					} elseif ($rule == 'maxvalue') {
						if ($data[$table][$field] > $rule_value) {
							$errors[$table][$field] =  $error ? $error : 'Eingabe zu groß (höchstens ' . $rule_value . ')';
							break;
						}
					} elseif ($rule == 'regexp') {
						if (!empty($data[$table][$field]) && !preg_match($rule_value, $data[$table][$field])) {
							$errors[$table][$field] =  $error ? $error : 'Ungültiges Format (' . $rule_value . ')';
							break;
						}
					} elseif ($rule == 'unique' && $rule_value) {
						if (empty($data[$table]['id'])) {
							$row = $this->db->findFirst('SELECT id FROM ' . $table . ' WHERE ' . $field . '=? LIMIT 1', [$data[$table][$field]]);
						} else {
							$row = $this->db->findFirst('SELECT id FROM ' . $table . ' WHERE ' . $field . '=? AND id!=? LIMIT 1', [$data[$table][$field], $data[$table]['id']]);
						}
						if (!empty($row)) {
							$errors[$table][$field] =  $error ? $error : 'Eintrag existiert bereits';
							break;
						}
					} elseif ($rule == 'exists') {
						$row = $this->db->findFirst('SELECT id FROM ' . $rule_value . ' WHERE id=? LIMIT 1', [$data[$table][$field]]);
						if (empty($row)) {
							$errors[$table][$field] =  $error ? $error : 'ID existiert nicht';
							break;
						}
					} elseif ($rule == 'function') {
						if (method_exists($this, $rule_value)) {
							$error = call_user_func([$this, $rule_value], $data, $table, $field);
							if ($error) {
								$errors[$table][$field] = $error;
								break;
							}
						} else {
							throw new Exception('Unkown validation function: ' . $rule_value);
						}
					} elseif ($rule == 'datetime') {
						if( $rule_value == 'time' ){
							$format = 'H:i';
						} elseif( $rule_value == 'date' ){
							$format = 'Y-m-d';
						} else{
							$format = 'Y-m-d H:i';
						}
						
						if( 
							($d = DateTime::createFromFormat( $format, $data[$table][$field] )) === false ||
							$d->format($format) !== $data[$table][$field]
						){
							$errors[$table][$field] =  $error ? $error : "Datum/Zeitstempel nicht gültig: '" . $format . "'";
							break;
						}
					} else {
						throw new Exception('Unkown validation rule: ' . $rule);
					}
				}
			}
		}
		return $errors;

	}

}
