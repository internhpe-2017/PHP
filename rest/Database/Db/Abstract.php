<?php

class Database_Db_Abstract {
	protected $db;

	public function __construct() {
		$this->db = Database_Database::connect(Config::DB_HOST, Config::DB_USER,
				Config::DB_PASS, Config::DB_TYPE, Config::DB_NEW_LINK);
		$this->db->selectDatabase(Config::DB_NAME);
		if($this->db->isError()) {
			throw new Exception('Cannot connect to database ' . $this->db->getError(), 500);
		}

		$Qutf8 = $this->db->query('SET NAMES utf8');
		$Qutf8->execute();

		$Qcollation = $this->db->query('SET COLLATION_CONNECTION=utf8_general_ci');
		$Qcollation->execute();
	}

	public function __destruct() {
		$this->db->disconnect();
	}
}
?>
