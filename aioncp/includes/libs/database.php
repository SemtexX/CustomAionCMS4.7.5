<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class database {
	
	public $offline;
	public $error;
	public $errorMessage;
	
	function __construct($host="localhost", $user="sa", $passwd="", $dbname="", $port="1433") {
		if(!check($dbname)) throw new Exception("Invalid Database Name");
		try {
			$this->db = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $user, $passwd, array(PDO::ATTR_TIMEOUT => 30, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
		} catch(PDOException $e) {
			$this->offline = true;
			$this->errorMessage = "[PDO] " . $e->getMessage();
		}
	}
	
	public function query($sql, $array='') {
		if(!is_array($array)) $array = array($array);
		$query = $this->db->prepare($sql);
		if (!$query) {
			$this->error = true;
			$this->errorMessage = $this->returnError();
			$query->closeCursor();
			return false;
		} else {
			if($query->execute($array)) {
				$query->closeCursor();
				return true;
			} else {
				$this->error = true;
				$this->errorMessage = $this->returnError($query);
				return false;
			}
		}
	}
	
	public function queryFetch($sql, $array='') {
		if(!is_array($array)) $array = array($array);
		$query = $this->db->prepare($sql);
		if (!$query) {
			$this->error = true;
			$this->errorMessage = $this->returnError();
			$query->closeCursor();
			return false;
		} else {
			if($query->execute($array)) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				$query->closeCursor();
				return (check($result)) ? $result : NULL;
			} else {
				$this->error = true;
				$this->errorMessage = $this->returnError($query);
				return false;
			}
		}
	}
	
	public function queryFetchSingle($sql, $array='') {
		$result = $this->queryFetch($sql, $array);
		return (isset($result[0])) ? $result[0] : NULL;
	}
	
	private function returnError($state='') {
		if(!check($state)) {
			$error = $this->db->errorInfo();
		} else {
			$error = $state->errorInfo();
		}
		return '[SQL '.$error[0].'] ['.$this->db->getAttribute(PDO::ATTR_DRIVER_NAME).' '.$error[1].'] > '.$error[2];
	}
	
	public function lastInsertId() {
		return $this->db->lastInsertId();
	}
}