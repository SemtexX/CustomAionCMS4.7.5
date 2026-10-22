<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$logs = $sdb->queryFetch("SELECT * FROM `log_command_add` ORDER BY `date` DESC LIMIT 500");

if(is_array($logs)) {
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Staff Name</th>';
			echo '<th>Player Name</th>';
			echo '<th>Item</th>';
			echo '<th>Count</th>';
			echo '<th>Date</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($logs as $row) {
		echo '<tr>';
			echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.$row['admin_name'].'">'.$row['admin_name'].'</a></td>';
			echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.$row['player_name'].'">'.$row['player_name'].'</a></td>';
			echo '<td>['.$row['item_id'].'] '.$row['item_name'].'</td>';
			echo '<td>'.$row['item_count'].'</td>';
			echo '<td>'.$row['date'].'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} else {
	
	message('There are no logs to display.', 'warning');
	
}