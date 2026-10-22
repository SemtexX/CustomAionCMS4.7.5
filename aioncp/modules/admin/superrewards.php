<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$logs = $db->queryFetch("SELECT * FROM `aioncms`.`superrewards_logs` ORDER BY `id` DESC");

if(is_array($logs)) {
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Transaction Id</th>';
			echo '<th>Payment Date</th>';
			echo '<th>Amount</th>';
			echo '<th>Username</th>';
			echo '<th>Error</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($logs as $row) {
		
		$error = $row['error'] == 1 ? '<span class="label label-danger">failed transaction</span>' : '';
		
		echo '<tr>';
			echo '<td>'.$row['transaction_id'].'</td>';
			echo '<td>'.$row['timestamp'].'</td>';
			echo '<td>$'.number_format($row['total'], 2).'</td>';
			echo '<td>'.$row['user_id'].'</td>';
			echo '<td>'.$error.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} else {
	
	message('There are no logs to display.', 'warning');
	
}