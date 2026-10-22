<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class RedeemCode {
	
	private $_codeMaxLen = 50;
	private $_codeTypes = array(
		'regular',
		'limited',
		'account'
	);
	
	private $_code;
	private $_codeType;
	private $_limit = null;
	private $_user = null;
	private $_configId;
	private $_reward;
	
	// codes:
	// 		regular: can be used by everyone, once
	//		limited: can be used N amount of times
	//		account: can be used by one account, once
	
	// CONSTRUCTOR
	
	function __construct() {
		
		// load database
		$this->db = Handler::loadDB();
		
	}
	
	// PUBLIC FUNCTIONS
	
	public function setCode($code) {
		if(strlen($code) > $this->_codeMaxLen) throw new Exception('The code exceeds the length limit.');
		$this->_code = $code;
	}
	
	public function setCodeType($type) {
		if(!in_array(strtolower($type), $this->_codeTypes)) throw new Exception('The code type is not valid.');
		$this->_codeType = strtolower($type);
	}
	
	public function setLimit($limit) {
		if(!Validator::UnsignedNumber($limit)) return;
		$this->_limit = $limit;
	}
	
	public function setUser($user) {
		$this->_user = $user;
	}
	
	public function setReward($reward) {
		if(!Validator::UnsignedNumber($reward)) return;
		$this->_reward = $reward;
	}
	
	public function addRewardCode() {
		if(!check($this->_code)) throw new Exception('Missing data, please complete all required fields.');
		if(!check($this->_codeType)) throw new Exception('Missing data, please complete all required fields.');
		
		if($this->_codeType == 'limited') {
			if(!check($this->_limit)) throw new Exception('Missing data, please complete all required fields.');
		} else {
			$this->_limit = null;
		}
		
		if($this->_codeType == 'account') {
			if(!check($this->_user)) throw new Exception('Missing data, please complete all required fields.');
		} else {
			$this->_user = null;
		}
		
		if(!check($this->_reward)) throw new Exception('Missing data, please complete all required fields.');
		
		if($this->_codeExists($this->_code)) throw new Exception('The same code already exists, please choose a different one.');
		
		$data = array(
			$this->_code,
			$this->_codeType,
			$this->_limit,
			$this->_user,
			$this->_reward
		);
		
		$result = $this->db->query("INSERT INTO `aioncms`.`redeem_codes` (redeem_code, redeem_type, redeem_limit, redeem_user, redeem_credit_amount) VALUES (?, ?, ?, ?, ?)", $data);
		if(!$result) throw new Exception('Could not add code.');
	}
	
	public function redeemCode() {
		if(!check($this->_code)) throw new Exception('Your code could not be redeemed, contact support.');
		
		// code data
		$redeemCodeData = $this->_getRedeemCodeData();
		if(!is_array($redeemCodeData)) throw new Exception('Your code could not be redeemed, contact support.');
		
		// code status
		if($redeemCodeData['status'] == 1) throw new Exception('The code you are trying to redeem has already expired.');
		
		// account data
		$Account = new Account();
		$Account->setUsername($_SESSION['username']);
		$Account->loadAccountDataFromUsername();
		$accountData = $Account->getAccountData();
		if(!is_array($accountData)) throw new Exception('Your account information could not be loaded.');
		$accountUsername = $accountData['name'];
		
		// code type
		switch($redeemCodeData['redeem_type']) {
			case 'regular':
				
				// check if user has already redeemed
				if($this->_hasUserRedeemedCode($redeemCodeData['id'], $accountUsername)) throw new Exception('You have already redeemed this code.');
				
				// reward user
				$Account->addCredits($redeemCodeData['redeem_credit_amount']);
				
				// add log
				$this->_addRedeemLog($redeemCodeData['id'], $accountUsername);
				
				break;
			case 'limited':
			
				// check if user has already redeemed
				if($this->_hasUserRedeemedCode($redeemCodeData['id'], $accountUsername)) throw new Exception('You have already redeemed this code.');
				
				// get redeem count
				$redeemCount = $this->_getCodeRedeemCount($redeemCodeData['id']);
				
				// check redeem limit
				if(!check($redeemCodeData['redeem_limit'])) throw new Exception('Your code could not be redeemed, contact support.');
				if($redeemCount >= $redeemCodeData['redeem_limit']) {
					$this->_disableRedeemCode($redeemCodeData['id']);
					throw new Exception('The code you are trying to redeem has already expired.');
				}
				
				// reward user
				$Account->addCredits($redeemCodeData['redeem_credit_amount']);
				
				// add log
				$this->_addRedeemLog($redeemCodeData['id'], $accountUsername);
				
				break;
			case 'account':
				
				// check if user has already redeemed
				if($this->_hasUserRedeemedCode($redeemCodeData['id'], $accountUsername)) throw new Exception('You have already redeemed this code.');
				
				// check user identifier
				if($redeemCodeData['redeem_user'] != $accountUsername) throw new Exception('Your account is not allowed to redeem this code.');
				
				// reward user
				$Account->addCredits($redeemCodeData['redeem_credit_amount']);
				
				// add log
				$this->_addRedeemLog($redeemCodeData['id'], $accountUsername);
				
				// disable code
				$this->_disableRedeemCode($redeemCodeData['id']);
				
				break;
			default:
				throw new Exception('Your code could not be redeemed, contact support.');
		}
		
	}
	
	public function getRedeemCodesList() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`redeem_codes` ORDER BY id DESC");
		if(!is_array($result)) return;
		return $result;
	}
	
	public function disableCode($id) {
		$this->_disableRedeemCode($id);
	}
	
	public function getLogs() {
		$result = $this->db->queryFetch("SELECT * FROM `aioncms`.`redeem_codes_logs` ORDER BY id DESC");
		if(!is_array($result)) return;
		return $result;
	}
	
	public function getUserLogs() {
		if(!check($this->_user)) return;
		
		$result = $this->db->queryFetch("SELECT `t1`.`id`, `t1`.`code_id`, `t1`.`date_redeemed`, `t1`.`user_identifier`, `t2`.`redeem_code`, `t2`.`redeem_credit_amount` FROM `aioncms`.`redeem_codes_logs` as `t1` INNER JOIN `aioncms`.`redeem_codes` as `t2` ON `t1`.`code_id` = `t2`.`id` WHERE `t1`.`user_identifier` = ? ORDER BY `t1`.`id` DESC", array($this->_user));
		if(!is_array($result)) return;
		return $result;
	}
	
	// PRIVATE FUNCTIONS
	
	private function _getRedeemCodeData() {
		if(!check($this->_code)) return;
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`redeem_codes` WHERE redeem_code = ?", array($this->_code));
		if(!is_array($result)) return;
		return $result;
	}
	
	private function _hasUserRedeemedCode($codeId, $userIdentifier) {
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`redeem_codes_logs` WHERE code_id = ? AND user_identifier = ?", array($codeId, $userIdentifier));
		if(!is_array($result)) return;
		return true;
	}
	
	private function _addRedeemLog($codeId, $userIdentifier) {
		$result = $this->db->query("INSERT INTO `aioncms`.`redeem_codes_logs` (code_id, date_redeemed, user_identifier) VALUES (?, now(), ?)", array($codeId, $userIdentifier));
		if(!$result) return;
		return true;
	}
	
	private function _getCodeRedeemCount($codeId) {
		$result = $this->db->queryFetchSingle("SELECT COUNT(*) as result FROM `aioncms`.`redeem_codes_logs` WHERE code_id = ?", array($codeId));
		if(!is_array($result)) return 0;
		return $result['result'];
	}
	
	private function _disableRedeemCode($codeId) {
		$result = $this->db->query("UPDATE `aioncms`.`redeem_codes` SET status = 1 WHERE id = ?", array($codeId));
		if(!$result) return;
		return true;
	}
	
	private function _codeExists($code) {
		$result = $this->db->queryFetchSingle("SELECT * FROM `aioncms`.`redeem_codes` WHERE redeem_code = ?", array($code));
		if(!is_array($result)) return;
		return true;
	}
	
}