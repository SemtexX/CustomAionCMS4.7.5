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
	
	$sdb = Handler::loadDB('siel');
	
	$result = $sdb->queryFetch("SELECT `players`.`id`, `players`.`name`, `players`.`exp`, `players`.`race`, `players`.`player_class`, `players`.`gender`, `abyss_rank`.`all_kill` FROM `abyss_rank` INNER JOIN `players` ON `abyss_rank`.`player_id` = `players`.`id` ORDER BY `abyss_rank`.`all_kill` DESC LIMIT 15", array());
	foreach($result as $row) {
		$rowData[] = implode(",", $row);
	}

	$cacheData = implode("||", $rowData);

	// Cache File Path
	$filePath = __PATH_CACHE__ . 'rankings.kills.siel.cache';

	// Save Data
	$fp = fopen($filePath, 'w');
	fwrite($fp, $cacheData);
	fclose($fp);

	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}