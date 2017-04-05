<?php

class Database_Mysql extends Database_Database {
	protected $use_transactions = false,
	$use_fulltext = false,
	$use_fulltext_boolean = false,
	$sql_parse_string = 'mysql_escape_string',
	$sql_parse_string_with_connection_handler = false;

	public function __construct($server, $username, $password, $new_link) {
		$this->server = $server;
		$this->username = $username;
		$this->password = $password;
		$this->new_link = $new_link;

		if (function_exists('mysql_real_escape_string')) {
			$this->sql_parse_string = 'mysql_real_escape_string';
			$this->sql_parse_string_with_connection_handler = true;
		}

		if ($this->is_connected === false) {
			$this->makeConnection();
		}
	}

	public function makeConnection() {
		if (defined('Config::USE_PCONNECT') && (Config::USE_PCONNECT === 1)) {
			$connect_function = 'mysql_pconnect';
		} else {
			$connect_function = 'mysql_connect';
		}

		if ($this->link = @$connect_function($this->server, $this->username, $this->password, $this->new_link)) {
			$this->setConnected(true);
			return true;
		} else {
			$this->setError(mysql_error(), mysql_errno());
			return false;
		}
	}

	public function disconnect() {
		if ($this->isConnected()) {
			if (@mysql_close($this->link)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function selectDatabase($database) {
		if ($this->isConnected()) {
			if (@mysql_select_db($database, $this->link)) {
				return true;
			} else {
				$this->setError(mysql_error($this->link), mysql_errno($this->link));
				return false;
			}
		} else {
			return false;
		}
	}

	public function parseString($value) {
		if ($this->sql_parse_string_with_connection_handler === true) {
			return call_user_func_array($this->sql_parse_string, array($value, $this->link));
		} else {
			return call_user_func_array($this->sql_parse_string, array($value));
		}
	}

	public function simpleQuery($query) {
		if($this->isConnected()) {
			$this->number_of_queries++;

			$resource = @mysql_query($query, $this->link);

			if($resource !== false) {
				$this->error = false;
				$this->error_number = null;
				$this->error_query = null;

				return $resource;
			} else {
				$this->setError(mysql_error($this->link), mysql_errno($this->link), $query);
				return false;
			}
		} else {
			return false;
		}
	}

	public function dataSeek($row_number, $resource) {
		return @mysql_data_seek($resource, $row_number);
	}

	public function randomQuery($query) {
		$query .= ' order by rand() limit 1';

		return $this->simpleQuery($query);
	}

	public function next($resource) {
		return @mysql_fetch_assoc($resource);
	}

	public function freeResult($resource) {
		if (@mysql_free_result($resource)) {
			return true;
		} else {
			$this->setError('Resource \'Database->' . $resource . '\' could not be freed.');
			return false;
		}
	}

	public function nextID() {
		if ( is_numeric($this->nextID) ) {
			$id = $this->nextID;
			$this->nextID = null;

			return $id;
		} elseif ($id = @mysql_insert_id($this->link)) {
			return $id;
		} else {
			$this->setError(mysql_error($this->link), mysql_errno($this->link));

			return false;
		}
	}

	public function numberOfRows($resource) {
		return @mysql_num_rows($resource);
	}

	public function affectedRows() {
		return mysql_affected_rows($this->link);
	}

	public function startTransaction() {
		$this->logging_transaction = true;

		if ($this->use_transactions === true) {
			return $this->simpleQuery('start transaction');
		}

		return false;
	}

	public function commitTransaction() {
		if ($this->logging_transaction === true) {
			$this->logging_transaction = false;
			$this->logging_transaction_action = false;
		}

		if ($this->use_transactions === true) {
			return $this->simpleQuery('commit');
		}

		return false;
	}

	public function rollbackTransaction() {
		if ($this->logging_transaction === true) {
			$this->logging_transaction = false;
			$this->logging_transaction_action = false;
		}

		if ($this->use_transactions === true) {
			return $this->simpleQuery('rollback');
		}

		return false;
	}

	public function setBatchLimit($sql_query, $from, $maximum_rows) {
		return $sql_query . ' limit ' . $from . ', ' . $maximum_rows;
	}

	public function getBatchSize($sql_query, $select_field = '*') {
		if (strpos($sql_query, 'SQL_CALC_FOUND_ROWS') !== false) {
			$bb = $this->query('select found_rows() as total');
		} else {
			$total_query = substr($sql_query, 0, strpos($sql_query, ' limit '));

			$pos_to = strlen($total_query);
			$pos_from = strpos($total_query, ' from ');

			if (($pos_group_by = strpos($total_query, ' group by ', $pos_from)) !== false) {
				if ($pos_group_by < $pos_to) {
					$pos_to = $pos_group_by;
				}
			}

			if (($pos_having = strpos($total_query, ' having ', $pos_from)) !== false) {
				if ($pos_having < $pos_to) {
					$pos_to = $pos_having;
				}
			}

			if (($pos_order_by = strpos($total_query, ' order by ', $pos_from)) !== false) {
				if ($pos_order_by < $pos_to) {
					$pos_to = $pos_order_by;
				}
			}

			$bb = $this->query('select count(' . $select_field . ') as total ' . substr($total_query, $pos_from, ($pos_to - $pos_from)));
		}

		return $bb->value('total');
	}

	public function prepareSearch($columns) {
		if ($this->use_fulltext === true) {
			return 'match (' . implode(', ', $columns) . ') against (:keywords' . (($this->use_fulltext_boolean === true) ? ' in boolean mode' : '') . ')';
		} else {
			$search_sql = '(';

			foreach ($columns as $column) {
				$search_sql .= $column . ' like :keyword or ';
			}

			$search_sql = substr($search_sql, 0, -4) . ')';

			return $search_sql;
		}
	}
        public function db_fetch($res)
	{
		$row=mysql_fetch_object($res);
		return $row;
	}




	public function db_rows($res)
	{
		$num=mysql_num_rows($res);
		return $num;
	}


	public  function db_insert_id()
	{
		$Id=mysql_insert_id();
		return $Id;
	}

	public  function db_fetchobject($s)
		{

			$row=mysql_fetch_object($s);
			if(is_array($row))
			{
				foreach($row as $key=>$val) {
				$row->$key=stripslashes($val);}
			}

			return $row;

		}


	public  function db_max_col_num( $col_name,$table)
	{
	
	        $bb = $this->query('select max(' . $col_name . ') as max_count ' . ' from '.$table);
	     
		if ( !is_numeric($bb->value('max_count') )) 
		{ 	
			return 1;
		}  
		else {
		       return $bb->value('max_count')+1; 
		}
	     
	    
	}
	
	
	public  function db_insert($table, $data)
	{
		$query = "INSERT INTO $table ";
		$fields ='';
		$values ='';

		foreach ($data as $fieldname => $fieldvalue)
		{
			$fields.= "$fieldname, ";
			if (strtolower($fieldvalue) == 'null')
			$values.= "NULL, ";
			elseif (strtolower($fieldvalue) == 'now()')
			$values.= "NOW(), ";
			else
			$values.= "'" . $this->db_escape($fieldvalue) . "', ";
		}

		$query .= "(" . rtrim($fields, ', ') . ") VALUES (" . rtrim($values, ', ') . ");";

			if ($this->db_query($query))
		    return mysql_insert_id();
		else
		    return - 1;
	}

	public  function db_update($query)
    {
        $this->sql = $query;

        mysql_query($this->sql, $this->con) or die(mysql_error());
        if (mysql_errno() > 0) {
            print $s . "<br>";
            die(mysql_error());
        }
        return;
    }

	public  function db_escape($string)
    {
        if (version_compare(phpversion(), "4.3.0") == "-1") {
            return mysql_escape_string($string);
        } else {
            return mysql_real_escape_string($string);
        }
    }

	public  function db_query($sql)
	{
		$res=mysql_query($sql);
		return $res;
	}
}
?>
