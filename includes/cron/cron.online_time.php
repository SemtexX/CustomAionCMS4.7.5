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
	
	$lastRun = file_get_contents(__PATH_CRON__ . 'onlinecron.txt');
	$lastRunTimestamp = intval($lastRun);
	$offset = time() - $lastRunTimestamp;
	if($offset < 1) die();
	if($offset > 90) {
		$offset = 70;
	}

	$macPool = array();
	$hddPool = array();

	// SIEL
	$sielPlayers = $sdb->queryFetch("SELECT `account_id` FROM `players` WHERE `online` = ?", array(1));
	if(is_array($sielPlayers)) {
		foreach($sielPlayers as $sPlayer) {
			# GET MAC AND HARDWAREID
			$accountData = $db->queryFetchSingle("SELECT `id`,`access_level`,`last_ip`,`last_mac` FROM `account_data` WHERE `id` = ?", array($sPlayer['account_id']));
			
			if($accountData['access_level'] == 0) {
				# VALIDATE MAC ADDRESS
				if(preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $accountData['last_mac'] != 1)) continue;
			}
			
			# SAVE TO POOL
			$macPool[$accountData['id']] = $accountData['last_mac'];
		}
	}

	// REMOVE DUPLICATES (PEOPLE LOGGED IN MULTIPLE ACCOUNTS IN SAME COMPUTER)
	$macList = array_unique($macPool);
	$hddList = array_unique($hddPool);

	if(is_array($sielPlayers)) {
		foreach($sielPlayers as $sPlayer) {
			if(!array_key_exists($sPlayer['account_id'], $macList)) continue;
			
			$check = $db->queryFetchSingle("SELECT * FROM `aioncms`.`players_onlinetime` WHERE `account_id` = ?", $sPlayer['account_id']);
			if(is_array($check)) {
				$data = array(
					'accountid' => $sPlayer['account_id'],
					'onlinetime' => $offset,
				);
				$query = "UPDATE `aioncms`.`players_onlinetime` SET total_onlinetime = total_onlinetime + :onlinetime WHERE account_id = :accountid";
				$run = $db->query($query, $data);
				continue;
			}
			
			$data = array(
				'accountid' => $sPlayer['account_id'],
				'onlinetime' => $offset,
			);
			$query = "INSERT INTO `aioncms`.`players_onlinetime` (account_id, total_onlinetime) VALUES (:accountid, :onlinetime)";
			$run = $db->query($query, $data);
		}
	}

	$fp = fopen(__PATH_CRON__ . 'onlinecron.txt', 'w+');
	fwrite($fp, time());
	fclose($fp);
	
	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}