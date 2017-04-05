<?php

class Database_Database {
	protected $is_connected = false,
	$link,
	$error_reporting = true,
	$error = false,
	$error_number,
	$error_query,
	$server,
	$username,
	$password,
	$new_link,
	$debug = false,
	$number_of_queries = 0,
	$time_of_queries = 0,
	$nextID = null,
	$logging_transaction = false,
	$logging_transaction_action = false;

	final public static function connect($server, $username, $password, $type, $new_link = 0) {
		//require_once('database/' . $type . '.php');
		$class = 'Database_' . $type;
		return new $class($server, $username, $password, $new_link);
	}

	protected function setConnected($boolean) {
		if ($boolean === true) {
			$this->is_connected = true;
		} else {
			$this->is_connected = false;
		}
	}

	protected function isConnected() {
		if ($this->is_connected === true) {
			return true;
		} else {
			return false;
		}
	}

	public function &query($query) {
		$database_Result = new Database_Result($this);
		$database_Result->setQuery($query);

		return $database_Result;
	}

	protected function setError($error, $error_number = '', $query = '') {
		if ($this->error_reporting === true) {
			$this->error = $error;
			$this->error_number = $error_number;
			$this->error_query = $query;
			Log::error($this->error . '[Query: "' . $this->error_query . '"]');
		}
	}

	public function isError() {
		if ($this->error === false) {
			return false;
		} else {
			return true;
		}
	}

	public function getError() {
		if ($this->isError()) {
			$error = '';
			if (!empty($this->error_number)) {
				$error .= $this->error_number . ': ';
			}
			$error .= $this->error;
			if (!empty($this->error_query)) {
				$error .= '; ' . htmlentities($this->error_query);
			}
			return $error;
		} else {
			return false;
		}
	}

	public function setErrorReporting($boolean) {
		if ($boolean === true) {
			$this->error_reporting = true;
		} else {
			$this->error_reporting = false;
		}
	}

	public function setDebug($boolean) {
		if ($boolean === true) {
			$this->debug = true;
		} else {
			$this->debug = false;
		}
	}

	function numberOfQueries() {
		return $this->number_of_queries;
	}

	function timeOfQueries() {
		return $this->time_of_queries;
	}

	function getMicroTime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}


class Database_Result {
	protected $db_class,
	$sql_query,
	$query_handler,
	$result,
	$rows,
	$affected_rows,
	$cache_key,
	$cache_expire,
	$cache_data,
	$cache_read = false,
	$debug = false,
	$batch_query = false,
	$batch_number,
	$batch_rows,
	$batch_size,
	$batch_to,
	$batch_from,
	$batch_select_field,
	$logging = false,
	$logging_module,
	$logging_module_id,
	$logging_fields = array(),
	$logging_changed = array();

	public function __construct(&$db_class) {
		$this->db_class =& $db_class;
	}

	public function setQuery($query) {
		$this->sql_query = $query;
	}

	public function appendQuery($query) {
		$this->sql_query .= ' ' . $query;
	}

	public function getQuery() {
		return $this->sql_query;
	}

	public function setDebug($boolean) {
		if ($boolean === true) {
			$this->debug = true;
		} else {
			$this->debug = false;
		}
	}

	private function valueMixed($column, $type = 'string') {
		if (!isset($this->result)) {
			$this->next();
		}

		switch ($type) {
			case 'int':
				return (int)$this->result[$column];
				break;
			case 'decimal':
				return (float)$this->result[$column];
				break;
			case 'string':
			default:
				return $this->result[$column];
		}
	}

	public function value($column) {
		return $this->valueMixed($column, 'string');
	}

	public function valueInt($column) {
		return $this->valueMixed($column, 'int');
	}

	public function valueDecimal($column) {
		return $this->valueMixed($column, 'decimal');
	}

	private function bindValueMixed($place_holder, $value, $type = 'string') {
		if('' === trim($value)) {
			$value = 'NULL';
			$type = 'raw';
		}
		switch($type) {
			case 'int':
				$value = intval($value);
				break;
			case 'float':
				$value = floatval($value);
				break;
			case 'raw':
				break;
			case 'string':
				default:
				$value = "'".$this->db_class->parseString(trim($value))."'";
		}

		$this->bindReplace($place_holder, $value);
	}

	private function bindReplace($place_holder, $value) {
		$pos = strpos($this->sql_query, $place_holder);

		if ($pos !== false) {
			$length = strlen($place_holder);
			$character_after_place_holder = substr($this->sql_query, $pos+$length, 1);

			if (($character_after_place_holder === false) || preg_match('{[\s,\)"]}', $character_after_place_holder)) {
				$this->sql_query = substr_replace($this->sql_query, $value, $pos, $length);
			}
		}
	}

	public function bindValue($place_holder, $value) {
		$this->bindValueMixed($place_holder, $value, 'string');
	}

	public function bindInt($place_holder, $value) {
		$this->bindValueMixed($place_holder, $value, 'int');
	}

	public function bindFloat($place_holder, $value) {
		$this->bindValueMixed($place_holder, $value, 'float');
	}

	public function bindRaw($place_holder, $value) {
		$this->bindValueMixed($place_holder, $value, 'raw');
	}

	public function bindTable($place_holder, $value) {
		$this->bindValueMixed($place_holder, $value, 'raw', false);
	}

	public function next() {
		if(!isset($this->query_handler)) {
			$this->execute();
		}

		$this->result = $this->db_class->next($this->query_handler);

		return $this->result;
	}

	public function freeResult() {
		if(preg_match('{^SELECT}i', $this->sql_query)) {
			$this->db_class->freeResult($this->query_handler);
		}
		unset($this);
	}

	public function numberOfRows() {
		if(!isset($this->rows)) {
			if(!isset($this->query_handler)) {
				$this->execute();
			}

			$this->rows = $this->db_class->numberOfRows($this->query_handler);
		}
		return $this->rows;
	}

	public function affectedRows() {
		if (!isset($this->affected_rows)) {
			if (!isset($this->query_handler)) {
				$this->execute();
			}

			$this->affected_rows = $this->db_class->affectedRows();
		}

		return $this->affected_rows;
	}

	public function execute() {
		$this->query_handler = $this->db_class->simpleQuery($this->sql_query);
		return $this->query_handler;
	}

	private function executeRandom() {
		return $this->query_handler = $this->db_class->randomQuery($this->sql_query);
	}

	private function executeRandomMulti() {
		return $this->query_handler = $this->db_class->randomQueryMulti($this->sql_query);
	}

	public function setCache($key, $expire = 0) {
		$this->cache_key = $key;
		$this->cache_expire = $expire;
	}

	public function setLogging($module, $id = null) {
		$this->logging = true;
		$this->logging_module = $module;
		$this->logging_module_id = $id;
	}

	public function toArray() {
		if (!isset($this->result)) {
			$this->next();
		}

		return $this->result;
	}

	public function setFullText() {
		$this->db_class->use_fulltest = true;
		$this->db_class->use_fulltext_boolean = true;
	}

	public function prepareSearch($keywords, $columns, $embedded = false) {
		if ($embedded === true) {
			$this->sql_query .= ' and ';
		}

		$keywords_array = explode(' ', $keywords);

		if ($this->db_class->use_fulltext === true) {
			if ($this->db_class->use_fulltext_boolean === true) {
				$keywords = '';

				foreach ($keywords_array as $keyword) {
					if ((substr($keyword, 0, 1) != '-') && (substr($keyword, 0, 1) != '+')) {
						$keywords .= '+';
					}

					$keywords .= $keyword . ' ';
				}

				$keywords = substr($keywords, 0, -1);
			}

			$this->sql_query .= $this->db_class->prepareSearch($columns);
			$this->bindValue(':keywords', $keywords);
		} else {
			foreach ($keywords_array as $keyword) {
				$this->sql_query .= $this->db_class->prepareSearch($columns);

				foreach ($columns as $column) {
					$this->bindValue(':keyword', '%' . $keyword . '%');
				}

				$this->sql_query .= ' and ';
			}

			$this->sql_query = substr($this->sql_query, 0, -5);
		}
	}

	public function setBatchLimit($batch_number = 1, $maximum_rows = 20, $select_field = '') {
		$this->batch_query = true;
		$this->batch_number = (is_numeric($batch_number) ? $batch_number : 1);
		$this->batch_rows = $maximum_rows;
		$this->batch_select_field = (empty($select_field) ? '*' : $select_field);

		$from = max(($this->batch_number * $maximum_rows) - $maximum_rows, 0);

		$this->sql_query = $this->db_class->setBatchLimit($this->sql_query, $from, $maximum_rows);
	}

	public function getBatchSize() {
		return $this->batch_size;
	}



      

}
?>