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
	
	$currentYear = (int) date("Y");
	$currentMonth = (int) date("m");
	
	$result = $db->queryFetch("SELECT * FROM `aioncms`.`votes_count` WHERE `character` IS NOT NULL AND `year` = ? AND `month` = ? AND `votes` > 0 ORDER BY `votes` DESC, `id` ASC", array($currentYear, $currentMonth));
	if(is_array($result)) {
		foreach($result as $row) {
			
			$playerInfo = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ? AND `account_id` = ?", array($row['character'], $row['id']));
			if(!is_array($playerInfo)) {
				# unlink character
				$unlink = $db->query("UPDATE `aioncms`.`votes_count` SET `character` = NULL, `last_update` = now() WHERE `id` = ? AND `year` = ? AND `month` = ?", array($row['id'], $currentYear, $currentMonth));
				
				# skip from ranking
				continue;
			}
			
			$rData = array(
				$playerInfo['name'],
				$playerInfo['exp'],
				$playerInfo['gender'],
				$playerInfo['race'],
				$playerInfo['player_class'],
				$row['votes']
			);
			
			$rowData[] = implode(",", $rData);
		}
	}
	
	$cacheData = implode("||", $rowData);

	// Cache File Path
	$filePath = __PATH_CACHE__ . 'rankings.votes.cache';

	// Save Data
	$fp = fopen($filePath, 'w');
	fwrite($fp, $cacheData);
	fclose($fp);

	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}