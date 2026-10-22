<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


class Login extends Account {
	
	public function accountLogin() {
		if(!check($this->_username)) throw new Exception('The username is required to login.');
		if(!check($this->_password)) throw new Exception('The password is required to login.');
		
		# load account data
		$this->loadAccountDataFromUsername();
		if(!is_array($this->_accountData)) throw new Exception('The account you entered is not valid.');
		
		# encrypt password
		$encryptedPassword = base64_encode(sha1($this->_password, true));
		
		# check password
		if($encryptedPassword != $this->_accountData['password']) throw new Exception('The password you entered is not valid.');
		
		$isStaffmember = ($this->_accountData['access_level'] > 0 ? true : false);
		sessionControl::newSession($this->_accountData['id'],$this->_accountData['name'],$this->_accountData['email'],$this->_accountData['ip_force'],$isStaffmember);
		
		logSystem::add('logged in');
		
		# redirect to usercp
		redirect('usercp/');
	}
}