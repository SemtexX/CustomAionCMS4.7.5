<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


class Register extends Account {
	
	private $_usernameMinLen = 6;
	private $_usernameMaxLen = 25;
	private $_passwordMinLen = 6;
	private $_passwordMaxLen = 25;
	private $_emailMaxLen = 50;
	
	
	function __construct() {
		$this->db = Handler::loadDB();
	}
	
	public function setUsername($input) {
		if(!check($input)) throw new Exception('Invalid username.');
		if(!Validator::Length($input, $this->_usernameMaxLen, $this->_usernameMinLen)) throw new Exception('Your username can be up to 25 characters long.');
		if(!Validator::AlphaNumeric($input)) throw new Exception('Your username can only contain numbers and letters.');
		
		$this->_username = $input;
		$this->_checkUsername();
	}
	
	public function setPassword($input) {
		if(!Validator::Length($input, $this->_passwordMaxLen, $this->_passwordMinLen)) throw new Exception('Your password can be up to 25 characters long.');
		
		$this->_password = $input;
		$this->_encryptPassword();
	}
	
	public function setEmail($input) {
		if(!Validator::Length($input, $this->_emailMaxLen)) throw new Exception('Your email can be up to 50 characters long.');
		if(!Validator::Email($input)) throw new Exception('The email address you entered is not valid.');
		
		$this->_email = $input;
		$this->_checkEmail();
	}
	
	public function setReferralId($userid) {
		if(!Validator::UnsignedNumber($userid)) return;
		$this->_referralId = $userid;
	}
	
	private function _encryptPassword() {
		if(!check($this->_password)) throw new Exception('Password not entered.');
		
		$this->_encryptedPassword =  base64_encode(sha1($this->_password, true));
	}
	
	private function _checkUsername() {
		if(!check($this->_username)) throw new Exception('Username not entered.');
		
		$this->loadAccountDataFromUsername();
		if(is_array($this->getAccountData())) throw new Exception('The username you entered cannot be used, please choose a new one.');
	}
	
	private function _checkEmail() {
		if(!check($this->_email)) throw new Exception('Email not entered.');
		
		$this->loadAccountDataFromEmail();
		if(is_array($this->getAccountData())) throw new Exception('The email address you entered is already in use. <a href="'.module_url('recovery/', true).'">Click here</a> to recover your account.');
	}
	
	public function createAccount() {
		if(!check($this->_username)) throw new Exception('Missing username.');
		if(!check($this->_encryptedPassword)) throw new Exception('Missing password.');
		if(!check($this->_email)) throw new Exception('Missing email.');
		if(is_array($this->getAccountData())) throw new Exception('Registration failed, try again.');
		
		# TODO: load last 20 or so accounts and check IP and username (levenshtein). If similar flag account for later check.
		
		$data = array(
			$this->_username,
			$this->_encryptedPassword,
			Handler::userIP(),
			$this->_email,
			1
		);
		
		$create = $this->db->query("INSERT INTO `account_data` (`name`, `password`, `last_ip`, `email`, `activated`, `created_at`) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)", $data);
		if(!$create) throw new Exception('There was problem creating your account, please contact support.');
		
		/*try {
			
			$verificationCode = md5(md5('338nr72o3rcn8g32fx') . md5($this->_username));
			$verificationLink = __PAGE_URL__ . 'verification/email/key/' . $verificationCode . '/';
			
			$email = new Email();
			$email->setTemplate('VERIFY_EMAIL');
			$email->addVariable('{USERNAME}', $this->_username);
			$email->addVariable('{VERIFICATION_LINK}', $verificationLink);
			$email->addAddress($this->_email);
			$email->send();
			
		} catch(Exception $ex) {
			throw new Exception('We could not send you the verification email, please contact support.');
		}*/
		
		// Referral system
		if(check($this->_referralId) && config('referral_active')) {
			$ReferralSystem = new ReferralSystem();
			$ReferralSystem->setUsername($this->_username);
			$ReferralSystem->setReferralId($this->_referralId);
			$ReferralSystem->saveReferral();
		}
		
	}
}