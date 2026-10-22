<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$logs = $db->queryFetch("SELECT * FROM `aioncms`.`paymentwall_logs` ORDER BY `id` DESC");

if(is_array($logs)) {
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Id</th>';
			echo '<th>User</th>';
			echo '<th>Cash</th>';
			echo '<th>Type</th>';
			echo '<th>Ref</th>';
			echo '<th>Signature</th>';
			echo '<th>Date</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($logs as $row) {		
		echo '<tr>';
			echo '<td>'.$row['id'].'</td>';
			echo '<td>'.$row['uid'].'</td>';
			echo '<td>'.$row['currency'].'</td>';
			echo '<td>'.$row['type'].'</td>';
			echo '<td>'.$row['ref'].'</td>';
			echo '<td>'.$row['sig'].'</td>';
			echo '<td>'.$row['timestamp'].'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} else {
	
	message('There are no logs to display.', 'warning');
	
}