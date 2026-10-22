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
	
	debug(date("Y-m-d H:i a", time()));
	
	$Lottery = new LotterySystem();
	$Lottery->cronJob();
	
	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}