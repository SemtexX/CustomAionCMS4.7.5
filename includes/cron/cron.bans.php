<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

# Access
define('access', 'cron');

# Path
$file_name = basename(__FILE__);
$sys_path = str_replace('\\','/',dirname(dirname(__FILE__))).'/';

# Load system
try {

	if(!@include_once($sys_path . 'system.php')) {
		throw new Exception('Could not load engine.');
	}
	
	$db = Handler::loadDB();
	
	$ctime = time();
	$cdate = date("Y-m-d H:i:s", $ctime);
	
	$banList = $db->queryFetch("SELECT * FROM `aioncms`.`temporal_bans` WHERE `staff_lock` = ? AND `end_date` < ?", array(0, $cdate));
	
	if(is_array($banList)) {
		foreach($banList as $ban) {
			
			if($ctime > strtotime($ban['end_date'])){
				
				# adding action to ban log
				$db->query("INSERT INTO `aioncms`.`messages` (`ban_id`, `date`, `message`, `type`, `ban_type`, `username`) VALUES (?, NOW(), ?, ?, ?, ?)", array($ban['id'], 'ACTION: Account automatically unbanned', 1, 0, $ban['staff']));
				
				# locking the ban
				$db->query("UPDATE `aioncms`.`temporal_bans` SET `user_lock` = ?, `staff_lock` = ?, `requires_action` = ? WHERE `id` = ?", array(1, 1, 0, $ban['id']));
				
				# unban the account
				$db->query("UPDATE `account_data` SET `ip_force` = NULL WHERE `name` = ?", array($ban['account']));
				
			}
			
		}
	}
	
	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}