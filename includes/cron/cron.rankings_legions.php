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
	
	$result = $sdb->queryFetch("SELECT `legions`.`id`, `legions`.`name`, `legions`.`level`, `legions`.`contribution_points` FROM `legions` ORDER BY `legions`.`contribution_points` DESC LIMIT 15", array());
	foreach($result as $row) {
		$legionMembers = $sdb->queryFetchSingle("SELECT COUNT(*) AS `members` FROM `legion_members` WHERE `legion_id` = ?", array($row['id']));
		$row['members'] = $legionMembers['members'];
		$rowData[] = implode(",", $row);
	}

	$cacheData = implode("||", $rowData);

	// Cache File Path
	$filePath = __PATH_CACHE__ . 'rankings.legions.siel.cache';

	// Save Data
	$fp = fopen($filePath, 'w');
	fwrite($fp, $cacheData);
	fclose($fp);

	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}