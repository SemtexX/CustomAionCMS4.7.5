<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$logs = $db->queryFetch("SELECT * FROM `aioncms`.`paypal` ORDER BY `id` DESC");

if(is_array($logs)) {
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Transaction Id</th>';
			echo '<th>Payment Date</th>';
			echo '<th>Amount</th>';
			echo '<th>Payer Email</th>';
			echo '<th>User Id</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($logs as $row) {
		echo '<tr>';
			echo '<td>'.$row['txn_id'].'</td>';
			echo '<td>'.$row['payment_date'].'</td>';
			echo '<td>$'.number_format($row['payment_gross'], 2).'</td>';
			echo '<td>'.$row['payer_email'].'</td>';
			echo '<td>'.$row['custom'].'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} else {
	
	message('There are no logs to display.', 'warning');
	
}