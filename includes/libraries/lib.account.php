<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


class Account {
	
	protected $_userid;
	protected $_username;
	protected $_password;
	protected $_email;
	protected $_accountData;
	protected $_extraSecurityData;
	
	function __construct() {
		$this->db = Handler::loadDB();
	}
	
	public function setId($input) {
		# TODO filters
		$this->_userid = $input;
		# TODO: check if account id exists
		$this->loadAccountDataFromId();
	}
	
	public function getId() {
		return $this->_userid;
	}
	
	public function setUsername($input) {
		# TODO filters
		$this->_username = $input;
	}
	
	public function getUsername() {
		return $this->_username;
	}
	
	public function setPassword($input) {
		# TODO filters
		$this->_password = $input;
	}
	
	public function getPassword() {
		return $this->_password;
	}
	
	public function setEmail($input) {
		# TODO filters
		$this->_email = $input;
	}
	
	public function getEmail() {
		return $this->_email;
	}
	
	public function loadAccountDataFromId() {
		if(!check($this->_userid)) return;
		
		# load account data
		$accountData = $this->db->queryFetchSingle("SELECT * FROM `account_data` WHERE `id` = ?", array($this->_userid));
		if(!is_array($accountData)) return;
		$this->_accountData = $accountData;
		
		# account security
		$this->_loadAccountSecurity();
	}
	
	public function loadAccountDataFromUsername() {
		if(!check($this->_username)) return;
		
		# load account data
		$accountData = $this->db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($this->_username));
		if(!is_array($accountData)) return;
		$this->_accountData = $accountData;
		
		# account security
		$this->_loadAccountSecurity();
	}
	
	public function loadAccountDataFromEmail() {
		if(!check($this->_email)) return;
		
		# load account data
		$accountData = $this->db->queryFetchSingle("SELECT * FROM `account_data` WHERE `email` = ?", array($this->_email));
		if(!is_array($accountData)) return;
		$this->_accountData = $accountData;
		
		# account security
		$this->_loadAccountSecurity();
	}
	
	public function getAccountData() {
		return $this->_accountData;
	}
	
	public function accountTypeTxt($input) {
		if(!check($input)) return 'Regular';
		if($input == 0) return 'Regular';
		if($input == 1) return 'Premium';
		if($input == 2) return 'VIP';
	}
	
	public function validatePassword($input) {
		if(!check($input)) return;
		if(!is_array($this->_accountData)) return;
		
		$encryptedPassword = base64_encode(sha1($input, true));
		
		if($encryptedPassword == $this->_accountData['password']) return true;
		return;
	}
	
	public function setPasswordChange($newPassword, $verificationCode) {
		if(!is_array($this->_accountData)) return;
		if(!check($newPassword, $verificationCode)) return;
		$NewPass = base64_encode(sha1($newPassword, true));		
		$setPasswordChange = $this->db->query("UPDATE `account_data` SET `hash` = ?, `confirmed` = ? WHERE `id` = ?", array($verificationCode, $newPassword, $this->_accountData['id']));
		$updatePassword = $this->db->query("UPDATE `account_data` SET `password` = ?, `hash` = NULL, `confirmed` = NULL WHERE `id` = ?", array($NewPass, $this->_accountData['id']));
		if(!$setPasswordChange) return;
		return true;
	}
	
	public function getExtraSecurityData() {
		return $this->_extraSecurityData;
	}
	
	public function changePassword() {
		if(!is_array($this->_accountData)) return;
		
		if(check($this->_password)) {
			# USE MANUALLY SET PASSWORD
			$encryptedPassword = base64_encode(sha1($this->_password, true));
			$updatePassword = $this->db->query("UPDATE `account_data` SET `password` = ?, `hash` = NULL, `confirmed` = NULL WHERE `id` = ?", array($encryptedPassword, $this->_accountData['id']));
			
			if($updatePassword) return true;
		} else {
			# USE PASSWORD FROM DATABASE (confirmed column)
			$encryptedPassword = base64_encode(sha1($this->_accountData['confirmed'], true));
			$updatePassword = $this->db->query("UPDATE `account_data` SET `password` = ?, `hash` = NULL, `confirmed` = NULL WHERE `id` = ?", array($encryptedPassword, $this->_accountData['id']));
			
			if($updatePassword) return true;
		}
		
		return;
	}
	
	private function _loadAccountSecurity() {
		if(!is_array($this->_accountData)) return;
		
		$accountSecurity = $this->db->queryFetchSingle("SELECT * FROM `account_security` WHERE `id` = ?", array($this->_accountData['id']));
		if(!is_array($accountSecurity)) {
			$this->_addExtraSecurityAccount();
			$accountSecurity = $this->db->queryFetchSingle("SELECT * FROM `account_security` WHERE `id` = ?", array($this->_accountData['id']));
		}
		
		$this->_extraSecurityData = $accountSecurity;
	}
	
	private function _addExtraSecurityAccount() {
		if(!is_array($this->_accountData)) return;
		
		if($this->_accountData['activated'] == 1) {
			$add = $this->db->query("INSERT INTO `account_security` (`id`, `email_confirmed`, `email_confirm_date`) VALUES (?, ?, now())", array($this->_accountData['id'], 1));
		} else {
			$add = $this->db->query("INSERT INTO `account_security` (`id`, `email_confirmed`) VALUES (?, ?)", array($this->_accountData['id'], 0));
		}
		
		if(!$add) return;
		return true;
	}
	
	public function activateAccount() {
		if(!is_array($this->_accountData)) return;
		
		# update account data
		$updateAccount = $this->db->query("UPDATE `account_data` SET `activated` = 1 WHERE `id` = ?", array($this->_accountData['id']));
		if(!$updateAccount) return;
		
		# update account security
		$updateSecurity = $this->db->query("UPDATE `account_security` SET `email_confirmed` = 1, `email_confirm_date` = now() WHERE `id` = ?", array($this->_accountData['id']));
		if(!$updateSecurity) return;
		
		return true;
	}
	
	public function setSecurityQuestions($question1, $answer1, $question2, $answer2) {
		if(!is_array($this->_accountData)) return;
		
		# update data
		$updateData = array(
			'userid' => $this->_accountData['id'],
			'q1' => $question1,
			'a1' => $answer1,
			'q2' => $question2,
			'a2' => $answer2,
		);
			
		# update account security
		$updateSecurity = $this->db->query("UPDATE `account_security` SET `question_1` = :q1, `answer_1` = :a1, `question_2` = :q2, `answer_2` = :a2, `questions_date` = now() WHERE `id` = :userid", $updateData);
		if(!$updateSecurity) return;
		
		return true;
	}
	
	public function setSecurityPIN($pin) {
		if(!is_array($this->_accountData)) return;
		
		# update data
		$updateData = array(
			'userid' => $this->_accountData['id'],
			'pin' => $pin,
		);
			
		# update account security
		$updateSecurity = $this->db->query("UPDATE `account_security` SET `security_pin` = :pin, `security_pin_date` = now() WHERE `id` = :userid", $updateData);
		if(!$updateSecurity) return;
		
		return true;
	}
	
	public function addCredits($amount=0) {
		if(!is_array($this->_accountData)) return;
		if(!Validator::UnsignedNumber($amount)) return;
		
		$add = $this->db->query("UPDATE `account_data` SET `toll` = `toll` + ? WHERE `id` = ?", array($amount, $this->_accountData['id']));
		if($add) return true;
		
		return;
	}
	
	public function subtractCredits($amount=0) {
		if(!is_array($this->_accountData)) return;
		if(!Validator::UnsignedNumber($amount)) return;
		
		$subtract = $this->db->query("UPDATE `account_data` SET `toll` = `toll` - ? WHERE `id` = ?", array($amount, $this->_accountData['id']));
		if($subtract) return true;
		
		return;
	}
	
	public function setPremium() {
		if(!is_array($this->_accountData)) return;
		if($this->_accountData['membership'] == 1) return;
		if($this->_accountData['old_membership'] == 1) return;
		
		if($this->_accountData['membership'] == 2) {
			$membership = 2;
			$old_membership = 1;
		} else {
			$membership = 1;
			$old_membership = 1;
		}
		
		$upgrade = $this->db->query("UPDATE `account_data` SET `membership` = ?, `old_membership` = ? WHERE `id` = ?", array($membership, $old_membership, $this->_accountData['id']));
		if($upgrade) return true;
		
		return;
	}
	
	public function setVip($days=30) {
		if(!is_array($this->_accountData)) return;
		if(!Validator::UnsignedNumber($days)) return;
		
		switch($this->_accountData['membership']) {
			case 0:
				$membership = 2;
				$old_membership = 0;
				break;
			case 1:
				$membership = 2;
				$old_membership = 1;
				break;
			case 2:
				$membership = 2;
				$old_membership = $this->_accountData['old_membership'];
				
				if(check($this->_accountData['expire'])) {
					$currentExpireTS = strtotime($this->_accountData['expire']);
					if($currentExpireTS > time()) {
						$extend = true;
					}
				}
				break;
			default:
				return;
		}
		
		if($extend == true) {
			# EXTEND VIP
			$expireTimestamp = $currentExpireTS+($days*86400);
			$expireDate = date("Y-m-d", $expireTimestamp);
		} else {
			# SET VIP
			$expireTimestamp = time()+($days*86400);
			$expireDate = date("Y-m-d", $expireTimestamp);
		}
		
		$upgrade = $this->db->query("UPDATE `account_data` SET `membership` = ?, `old_membership` = ?, `expire` = ? WHERE `id` = ?", array($membership, $old_membership, $expireDate, $this->_accountData['id']));
		if($upgrade) return true;
		
		return;
	}
	
	public function getVotes() {
		if(!is_array($this->_accountData)) return;
		
		$voteData = $this->db->queryFetch("SELECT * FROM `aioncms`.`votes` WHERE `name` = ?", array($this->_accountData['name']));
		if(!is_array($voteData)) return;
		
		$return = array();
		foreach($voteData as $vote) {
			$return[$vote['site']] = array(
				'newdate' => $vote['newdate'],
				'ip' => $vote['ip'],
				'mac' => $vote['mac']
			);
		}
		
		return $return;
	}
	
	public function resetAccountSecurity() {
		if(!is_array($this->_accountData)) return;
		
		# update account security
		$updateSecurity = $this->db->query("UPDATE `account_security` SET `question_1` = NULL, `answer_1` = NULL, `question_2` = NULL, `answer_2` = NULL, `questions_date` = NULL, `security_pin` = NULL, `security_pin_date` = NULL WHERE `id` = ?", array($this->_accountData['id']));
		if(!$updateSecurity) return;
		
		return true;
	}
}