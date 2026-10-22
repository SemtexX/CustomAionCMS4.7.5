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
	
	// clean old active list
	$db->query("DELETE FROM `aioncms`.`weeklyspecial_activeitems`", array());

	// select new items
	$itemList = $db->queryFetch("SELECT * FROM `aioncms`.`weeklyspecial_itemlist` ORDER BY RAND() LIMIT 15", array());
	if(is_array($itemList)) {
		foreach($itemList as $item) {
			$quantity = 5;
			$costRnd = mt_rand(1,10);
			if(rand(1,2) == 1) {
				// lower price
				$cost = ceil($item['cost']-($item['cost']*($costRnd/100)));
			} else {
				// price goes up
				$cost = ceil($item['cost']+($item['cost']*($costRnd/100)));
			}
			
			// add item
			$addItem = $db->query("INSERT INTO `aioncms`.`weeklyspecial_activeitems` (`item_id`, `quantity`, `cost`, `item_qty`) VALUES (?, ?, ?, ?)", array($item['item_id'], $quantity, $cost, $item['qty']));
			
		}
	}

	echo '1';
	
} catch(Exception $ex) {

	die($ex->getMessage());
	
}