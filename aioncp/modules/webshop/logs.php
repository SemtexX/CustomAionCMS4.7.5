<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$logs = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_logs` ORDER BY `id` DESC LIMIT 50");

if(is_array($logs)) {
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Account</th>';
			echo '<th>Player</th>';
			echo '<th>Ip Address</th>';
			echo '<th>Date</th>';
			echo '<th>Item</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($logs as $row) {
		
		$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['item']));
		$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
			
		echo '<tr>';
			echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['acc'].'" target="_blank">'.$row['acc'].'</a></td>';
			echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.$row['name'].'" target="_blank">'.$row['name'].'</a></td>';
			echo '<td>'.$row['ip'].'</td>';
			echo '<td>'.date("Y-m-d H:i:s A", $row['time']).'</td>';
			echo '<td>['.$row['item'].'] '.$itemName.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} else {
	
	message('There are no logs to display.', 'warning');
	
}