<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class logSystem {
	
	public static function add($message="", $severity=10) {
		$db = Handler::loadDB();
		
		$logAccount = (check($_SESSION['username']) ? $_SESSION['username'] : 'guest');
		$logIp = Handler::userIP();
		$logLocation = (check($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown');
		
		$logData = array(
			'account' => $logAccount,
			'ip' => $logIp,
			'location' => $logLocation,
			'message' => $message
		);
		
		try {
			$addLog = $db->query("INSERT INTO `aioncms`.`web_logs` (`account`, `ip_address`, `location`, `message`, `timestamp`) VALUES (:account, :ip, :location, :message, now())", $logData);
			if(!$addLog) return;
		} catch(Exception $ex) {
			return;
		}
		
		return true;
	}
	
}