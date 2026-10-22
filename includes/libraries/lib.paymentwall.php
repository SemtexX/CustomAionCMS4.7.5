<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class Paymentwall {
	
	protected $_requiredData = array(
		'uid',
		'currency',
		'type',
		'ref',
		'sig'
	);
	
	function __construct() {
		
		// database object
		$this->db = Handler::loadDB();
		
	}
	
	/**
	 * processPayment
	 * 
	 */
	public function processPayment($data) {
		if(!is_array($data)) return;
		if(!$this->_checkRequiredData($data)) return;
		
		// account data
		$Account = new Account();
		$Account->setUsername($data['uid']);
		$Account->loadAccountDataFromUsername();
		$accountData = $Account->getAccountData();
		if(!is_array($accountData)) throw new Exception('error 1');
		
		// send credits
		try {
			
			$Account->addCredits($data['currency']);
			
		} catch(Exception $ex) {
			// TODO: log system
			throw new Exception($ex->getMessage());
		}
		
		// save log
		$this->_saveLog($data);
		
	}
	
	/**
	 * processCancelation
	 * 
	 */
	public function processCancelation($data) {
		if(!is_array($data)) return;
		if(!$this->_checkRequiredData($data)) return;
		
		// account data
		$Account = new Account();
		$Account->setUsername($data['uid']);
		$Account->loadAccountDataFromUsername();
		$accountData = $Account->getAccountData();
		if(!is_array($accountData)) throw new Exception('error 2');
		
		// send credits
		try {
			
			$Account->subtractCredits(abs($data['currency']));
			
		} catch(Exception $ex) {
			// TODO: log system
			throw new Exception($ex->getMessage());
		}
		
		// save log
		$this->_saveLog($data);
		
	}
	
	/**
	 * processPending
	 * 
	 */
	public function processPending($data) {
		if(!is_array($data)) return;
		if(!$this->_checkRequiredData($data)) return;
		
		// save log
		$this->_saveLog($data);
		
	}
	
	/**
	 * getLogs
	 * 
	 */
	public function getLogs() {
		$logs = $this->db->queryFetch("SELECT * FROM `aioncms`.`paymentwall_logs` ORDER BY `id` DESC");
		if(!is_array($logs)) return;
		return $logs;
	}
	
	/**
	 * _saveLog
	 * 
	 */
	private function _saveLog($data) {
		$logData = array(
			'uid' => $data['uid'],
			'currency' => $data['currency'],
			'type' => $data['type'],
			'ref' => $data['ref'],
			'sig' => $data['sig']
		);
		
		$query = "INSERT INTO `aioncms`.`paymentwall_logs` (`uid`, `currency`, `type`, `ref`, `sig`, `timestamp`) VALUES (:uid, :currency, :type, :ref, :sig, now())";
		
		$log = $this->db->query($query, $logData);
		if(!$log) return;
	}
	
	/**
	 * _checkRequiredData
	 * 
	 */
	private function _checkRequiredData($data) {
		foreach($this->_requiredData as $key) {
			if(!array_key_exists($key, $data)) return;
		}
		return true;
	}
	
}