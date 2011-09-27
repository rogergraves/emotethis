<?php

class DbException extends Exception { }




class DB
{
	private static $db = null;
	
	function __construct() {
		$this->connect();
	}
	
	public function connect(){
		
		if(DB::$db) return DB::$db;
		
		DB::$db = mysql_connect(DBHOST, DBUSER, DBPASS);
		mysql_select_db(DBNAME);
		if (!DB::$db || !is_resource(DB::$db)) {
			throw new DbException("Db error " . mysql_errno(DB::$db) . ": " . mysql_error(DB::$db) );
		}
		mysql_set_charset('utf8');
	}
	
	public static function mysql_escape($value){
		return "'" . mysql_real_escape_string($value) . "'";
	}
	
	public function mysql_datetime($timestamp = null){
		if(!isset($timestamp)) $timestamp = time();
		return date('Y-m-d H:i:s', $timestamp);
	}
	
	public function datetime_to_unix($datetime) {
		list($date, $time) = explode(' ', $datetime);
		list($year, $month, $day) = explode('-', $date);
		list($hour, $minute, $second) = explode(':', $time);
		return mktime($hour, $minute, $second, $month, $day, $year);
	}
	
	public function runQuery($sql, $return = true,$fetchObj = false){
		//Run the Query
		//echo("DB: " . DB::$db);
		$result = mysql_query($sql,DB::$db);
		
		//echo("SQL: $sql");
		
		//If error occured stop
		if( ! $result ){
			// IN: it is better to throw an exception here instead of die
			throw new DbException("Db error for: " . $sql . " - " . mysql_errno(DB::$db) . ": " . mysql_error(DB::$db) );
		}
		//Return only if it is wanted
		if ($return){
			if($fetchObj) return new FetchRow($result);
			$returnResult = array();
			while($row = mysql_fetch_assoc($result)){
				$returnResult[] = $row;
			}
			return $returnResult;
		}else{
			return $result;
		}
	}
	
	public function last_insert_id(){
		return mysql_insert_id();
	}
	
	public function arrayInsert($table, array $bind){
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		foreach ($bind as $col => $val) {
			$cols[] = $col;
			if (!is_numeric($val)) {
				$vals[] = "'" . mysql_real_escape_string($val) . "'";
			} else {
				$vals[] = $val;
			}
		}

		// build the statement
		$sql = "INSERT INTO " . $table . ' (' . implode(', ', $cols) . ') ' . 'VALUES (' . implode(', ', $vals) . ')';
		// execute
		$this->runQuery($sql, false);
	}
	
}

class FetchRow{

	protected $result = null;

	function __construct($result) {
	    $this->result = $result;
	}

	public function nextRow(){
	    return mysql_fetch_assoc($this->result);
	}
}


class HomieDB{
	private static $db = null;
	
	function __construct() {
		$this->connect();
	}
	
	public function connect(){
		
		if(HomieDB::$db) return HomieDB::$db;
		//print HOMIE_DBHOST.", ".HOMIE_DBUSER.", ".HOMIE_DBPASS;
		HomieDB::$db = mysql_connect(HOMIE_DBHOST, HOMIE_DBUSER, HOMIE_DBPASS);
		mysql_select_db(HOMIE_DBNAME);
		if (!HomieDB::$db || !is_resource(HomieDB::$db)) {
			throw new DbException("Db error " . mysql_errno(HomieDB::$db) . ": " . mysql_error(HomieDB::$db) );
		}
		mysql_set_charset('utf8');
	}
	
	public function runQuery($sql, $return = true,$fetchObj = false){
		$result = mysql_query($sql,HomieDB::$db);

		if( ! $result ){
			throw new DbException("Db error for: " . $sql . " - " . mysql_errno(HomieDB::$db) . ": " . mysql_error(HomieDB::$db) );
		}
		//Return only if it is wanted
		if ($return){
			if($fetchObj) return new FetchRow($result);
			$returnResult = array();
			while($row = mysql_fetch_assoc($result)){
				$returnResult[] = $row;
			}
			return $returnResult;
		}else{
			return $result;
		}
	}
}
?>