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

	if(!@include_once($sys_path . 'includes/system.php')) {
		throw new Exception('Could not load engine.');
	}
	
	if(!check($_GET['key']))  throw new Exception('no key');
	if($_GET['key'] != 'WJaddps23DZM')  throw new Exception('bad key');
	
	$sdb = Handler::loadDB('siel');
	
	$legionid = $_GET['legionid'];
	
	$legionEmblem = $sdb->queryFetchSingle("SELECT * FROM `legion_emblems` WHERE `legion_id` = ?", array($legionid));
	if(!is_array($legionEmblem)) throw new Exception('no legion');
	
	$data = $legionEmblem['emblem_data'];

	header("Content-type: image/dds");
	echo $data;
	
} catch(Exception $ex) {
	die('0');
}