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
	$sdb = Handler::loadDB('siel');

	$taskStart = microtime(true);

	$onlinePlayers = $sdb->queryFetch("SELECT * FROM `players` WHERE `online` = ?", array(1));

	if(is_array($onlinePlayers)) {
		foreach($onlinePlayers as $player) {
			$inventoryItems = $sdb->queryFetch("SELECT * FROM inventory WHERE item_owner = ?", array($player['id']));
				if(is_array($inventoryItems)) {
					foreach($inventoryItems as $item) {
						
						$checkDB = $db->queryFetchSingle("SELECT * FROM `aioncms`.`item_tracker_list` WHERE `item_unique_id` = ?", array($item['item_unique_id']));
						if(is_array($checkDB)) {
							
							# ITEM IS BEING TRACKED, CHECK OWNER
							if($checkDB['item_owner'] != $item['item_owner']) {
								
								# GET OLD OWNER INFO
								$oldOwnerInfo = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($checkDB['item_owner']));
								if(is_array($oldOwnerInfo)) {
									$oldOwnerPlayerName = $oldOwnerInfo['name'];
								} else {
									$oldOwnerPlayerName = NULL;
								}
								
								# GET NEW OWNER INFO
								$newOwnerInfo = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($item['item_owner']));
								if(is_array($newOwnerInfo)) {
									$newOwnerPlayerAccount = $newOwnerInfo['account_name'];
									$newOwnerPlayerName = $newOwnerInfo['name'];
								} else {
									$newOwnerPlayerAccount = NULL;
									$newOwnerPlayerName = NULL;
								}
								
								# NEW OWNER, UPDATE TRACKER INFO
								$updateTracker = $db->query("UPDATE `aioncms`.`item_tracker_list` SET `item_owner` = ?, `item_count` = ?, `item_enchant` = ?, `item_tempering` = ?, `last_change` = now() WHERE `item_unique_id` = ?", array($item['item_owner'], $item['item_count'], $item['enchant'], $item['tempering'], $item['item_unique_id']));
								if(!$updateTracker) {
									echo 'Error ['.$item['item_unique_id'].'][U]<br />';
								}
								
								# ADD NEW OWNER LOG
								$logData = array(
									$item['item_unique_id'],
									$item['item_id'],
									$player['account_name'], // old owner account
									$oldOwnerPlayerName, // old owner name (character)
									$checkDB['item_owner'],
									$newOwnerPlayerAccount,
									$newOwnerPlayerName,
									$item['item_owner'],
									1
								);
								$trackerLog = $db->query("INSERT INTO `aioncms`.`item_tracker_logs` (`item_unique_id`, `item_id`, `old_owner_account`, `old_owner_name`, `old_owner`, `new_owner_account`, `new_owner_name`, `new_owner`, `log_date`, `log_type`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, now(), ?)", $logData);
								if(!$trackerLog) {
									echo 'Error ['.$item['item_unique_id'].'][L]<br />';
								}
							}
							
						} else {
							
							# ADD ITEM TO TRACKER SYSTEM
							$trackItem = $db->query("INSERT INTO `aioncms`.`item_tracker_list` (`item_unique_id`, `item_owner`, `item_count`, `item_enchant`, `item_tempering`, `list_date`) VALUES (?, ?, ?, ?, ?, now())", array($item['item_unique_id'], $item['item_owner'], $item['item_count'], $item['enchant'], $item['authorize']));
							if(!$trackItem) {
								echo 'Error ['.$item['item_unique_id'].'][A]<br />';
							}
						}
						
					}
				}
			
		}
	}

	$taskEnd = microtime(true);
	
	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}