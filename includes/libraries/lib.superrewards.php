<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class SuperRewards {
	
	protected $_secretKey;
	protected $_id;
	protected $_uid;
	protected $_oid;
	protected $_total;
	protected $_sig;
	
	function __construct($secretKey="") {
		
		// database object
		$this->db = Handler::loadDB();
		
		// configs
		$this->_secretKey = $secretKey;
		
	}
	
	/**
	 * setTransactionId
	 * 
	 */
	public function setTransactionId($id) {
		if(!Validator::UnsignedNumber($id)) throw new Exception('error');
		$this->_id = $id;
	}
	
	/**
	 * setUserId
	 * 
	 */
	public function setUserId($id) {
		$this->_uid = $id;
	}
	
	/**
	 * setOfferId
	 * 
	 */
	public function setOfferId($id) {
		$this->_oid = $id;
	}
	
	/**
	 * setTotal
	 * 
	 */
	public function setTotal($total) {
		if(!Validator::Number($total)) throw new Exception('error');
		$this->_total = $total;
	}
	
	/**
	 * setSignatureHash
	 * 
	 */
	public function setSignatureHash($sig) {
		if(!Validator::AlphaNumeric($sig)) throw new Exception('error');
		$this->_sig = $sig;
	}
	
	/**
	 * validateHash
	 * 
	 */
	public function validateHash() {
		if(!check($this->_sig)) return;
		$hash = $this->_newHash();
		if(!check($hash)) return;
		if($hash != $this->_sig) return;
		return true;
	}
	
	/**
	 * processPayment
	 * 
	 */
	public function processPayment() {
		if(!check($this->_id)) return;
		if(!check($this->_uid)) return;
		if(!check($this->_oid)) return;
		if(!check($this->_total)) return;
		if(!check($this->_sig)) return;
		
		// account data
		try {
			$Account = new Account();
			$Account->setUsername($this->_uid);
			$Account->loadAccountDataFromUsername();
			$accountData = $Account->getAccountData();
			if(!is_array($accountData)) {
				throw new Exception('error');
			}
		} catch(Exception $ex) {
			$this->saveErrorLog();
			return;
		}
		
		if($this->_total >= 1) {
			// add cash
			$addCash = $Account->addCredits($this->_total);
			if(!$addCash) {
				$this->saveErrorLog();
				return;
			}
		} else {
			// subtract cash
			$subtractCash = $Account->subtractCredits($this->_total);
			if(!$subtractCash) {
				$this->saveErrorLog();
				return;
			}
		}
		
		// Log
		$data = array(
			'tid' => $this->_id,
			'uid' => $this->_uid,
			'oid' => $this->_oid,
			'total' => $this->_total,
			'sig' => $this->_sig
		);
		
		$query = "INSERT INTO `aioncms`.`superrewards_logs` (`transaction_id`, `user_id`, `offer_id`, `total`, `signature`, `timestamp`) VALUES (:tid, :uid, :oid, :total, :sig, CURRENT_TIMESTAMP)";
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
	/**
	 * saveErrorLog
	 * 
	 */
	public function saveErrorLog() {
		if(!check($this->_id)) return;
		if(!check($this->_uid)) return;
		if(!check($this->_oid)) return;
		if(!check($this->_total)) return;
		if(!check($this->_sig)) return;
		
		$data = array(
			'tid' => $this->_id,
			'uid' => $this->_uid,
			'oid' => $this->_oid,
			'total' => $this->_total,
			'sig' => $this->_sig,
			'error' => 1
		);
		
		$query = "INSERT INTO `aioncms`.`superrewards_logs` (`transaction_id`, `user_id`, `offer_id`, `total`, `signature`, `timestamp`, `error`) VALUES (:tid, :uid, :oid, :total, :sig, CURRENT_TIMESTAMP, :error)";
		
		$result = $this->db->query($query, $data);
		if(!$result) return;
		
		return true;
	}
	
	/**
	 * getLogs
	 * 
	 */
	public function getLogs() {
		$logs = $this->db->queryFetch("SELECT * FROM `aioncms`.`superrewards_logs` ORDER BY `id` DESC");
		if(!is_array($logs)) return;
		return $logs;
	}
	
	/**
	 * isPostbackDuplicate
	 * 
	 */
	public function isPostbackDuplicate() {
		if(!check($this->_id)) return;
		
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`superrewards_logs` WHERE `transaction_id` = ? AND `error` = ?", array($this->_id, 0));
		if(!is_array($result)) return;
		return true;
	}
	
	/**
	 * _newHash
	 * 
	 */
	protected function _newHash() {
		if(!check($this->_id)) return;
		if(!check($this->_total)) return;
		if(!check($this->_uid)) return;
		if(!check($this->_secretKey)) return;
		
		return md5($this->_id.':'.$this->_total.':'.$this->_uid.':'.$this->_secretKey);
	}
	
}