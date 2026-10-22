<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

class sessionControl {
	
	private static $db;
	
	/**
	 * newSession
	 * deletes the guest session data, regenerates the session id and creates a new user session
	 * 
	 * @param int $userid
	 * @param string $username
	 * @param string $email
	 */
	public static function newSession($userid,$username,$email,$ipforce,$isstaff) {
		self::deleteSession(session_id());
		$_SESSION['guest'] = false;
		$_SESSION['failed_logins'] = 0;
		
		session_regenerate_id();
		$_SESSION['valid'] = true;
		$_SESSION['userid'] = $userid;
		$_SESSION['username'] = $username;
		$_SESSION['email'] = $email;
		$_SESSION['ip_force'] = $ipforce;
		$_SESSION['is_staff'] = $isstaff;
		
		self::deleteMultipleSessions($userid);
		$data = array($userid, session_id(), $_SESSION['last_location'], Handler::userIP());
		
		try {
			self::$db->query("INSERT INTO `aioncms`.`website_session_control` (userid,session_id,last_location,session_ip,active) VALUES (?, ?, ?, ?, now())", $data);
		} catch(Exception $ex) {
			// Log system: session control error log
		}
		//redirect();
	}
	
	/**
	 * isLoggedIn
	 * checks if user session data exists
	 * 
	 * @return boolean
	 */
	public static function isLoggedIn() {
		if(check($_SESSION['valid'], $_SESSION['userid'], $_SESSION['username'], $_SESSION['email'])) {
			return true;
		}
		return false;
	}
	
	/**
	 * logout
	 * deletes the session data from the database and destroys the session data
	 */
	public static function logout() {
		self::deleteSession(session_id());
		session_unset($_SESSION['valid']);
		session_unset($_SESSION['userid']);
		session_unset($_SESSION['username']);
		session_unset($_SESSION['email']);
		session_unset($_SESSION['ip_force']);
		session_destroy();
		redirect();
	}
	
	/**
	 * initSessionControl
	 * initiates the session control system
	 * 
	 * @param string $type
	 */
	public static function initSessionControl(database $db, $type="") {
		self::$db = $db;
		switch($type) {
			case "user":
				$sessionData = self::sessionInfo(session_id(),"sessionid");
				if(!is_array($sessionData)) {
					self::logout();
				} else {
					//if($sessionData['session_ip'] != Handler::userIP()) {
					//	self::logout();
					//}
					self::isSessionIDLE($sessionData['active']);
					self::updateSession("user");
				}
				break;
			default:
				if($_SESSION['guest']) {
					$sessionData = self::sessionInfo(session_id(),"sessionid");
					if(!is_array($sessionData)) {
						self::newGuestSession();
					} else {
						self::updateSession();
					}
				} else {
					self::newGuestSession();
				}
		}
	}
	
	/**
	 * sessionInfo
	 * returns the session information from the database
	 * 
	 * @param string $identifier
	 * @param string $data
	 * @return array
	 */
	private static function sessionInfo($data,$identifier="") {
		if(!check($data)) return;
		switch($identifier) {
			case "sessionid":
				$query = "SELECT * FROM `aioncms`.`website_session_control` WHERE session_id = ?";
				break;
			default:
				$query = "SELECT * FROM `aioncms`.`website_session_control` WHERE userid = ?";
		}
		try {
			return self::$db->queryFetchSingle($query, array($data));
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * newGuestSession
	 * creates a new guest session in the database
	 * 
	 * @return boolean
	 */
	private static function newGuestSession() {
		$_SESSION['guest'] = true;
		try {
			$data = array('-1', session_id(), $_SESSION['last_location'], Handler::userIP());
			self::$db->query("INSERT INTO `aioncms`.`website_session_control` (userid,session_id,last_location,session_ip,active) VALUES (?, ?, ?, ?, now())", $data);
			return true;
		} catch(Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * lastUserLocation
	 * updates the last user location
	 * 
	 * @param string $location
	 */
	public static function lastUserLocation($location) {
		$_SESSION['last_location'] = (check($location) ? $location : "home/");
	}
	
	/**
	 * updateSession
	 * updates the session information in the database
	 * 
	 * @param string $type
	 * @return boolean
	 */
	private static function updateSession($type="") {
		switch($type) {
			case "user":
				$data = array($_SESSION['last_location'], session_id());
				$query = "UPDATE `aioncms`.`website_session_control` SET last_location = ?, active = now() WHERE session_id = ?";
				break;
			default:
				$data = array($_SESSION['last_location'], Handler::userIP(), session_id());
				$query = "UPDATE `aioncms`.`website_session_control` SET last_location = ?, session_ip = ?, active = now() WHERE session_id = ?";
		}
		try {
			self::$db->query($query, $data);
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * deleteSession
	 * deletes the session information from the database
	 * 
	 * @param string $sessionid
	 * @return boolean
	 */
	private static function deleteSession($sessionid) {
		if(!check($sessionid)) return;
		try {
			self::$db->query("DELETE FROM `aioncms`.`website_session_control` WHERE session_id = ?", array($sessionid));
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * deleteMultipleSessions
	 * deletes the session information of a specific user id from the database
	 * 
	 * @param int $userid
	 * @return boolean
	 */
	private static function deleteMultipleSessions($userid) {
		if(!check($userid)) return;
		try {
			self::$db->query("DELETE FROM `aioncms`.`website_session_control` WHERE userid = ?", array($userid));
			return true;
		} catch (Exception $ex) {
			// Log system: session control error log
		}
	}
	
	/**
	 * isSessionIDLE
	 * checks if a session is idle for over 5 minutes and logouts the user
	 * 
	 * @param datetime $last_action
	 */
	private static function isSessionIDLE($last_action) {
		$lastAction = strtotime($last_action);
		$idleTime = time() - $lastAction;
		if($idleTime >= 300) {
			//self::logout();
		}
	}
}