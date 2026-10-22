<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

message('
<strong>Regular:</strong> Can be redeemed by everyone once.<br />
<strong>Limited:</strong> Can be used by everyone until the use limit is reached.<br />
<strong>Account:</strong> Can be used by a single user once.<br />
', 'info');

$RedeemCode = new RedeemCode();
$logsList = $RedeemCode->getLogs();

echo '<table class="table">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Code Id</th>';
			echo '<th>Date Redeemed</th>';
			echo '<th>Account</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
		
	foreach($logsList as $logData) {
		echo '<tr>';
			echo '<td>'.$logData['code_id'].'</td>';
			echo '<td>'.$logData['date_redeemed'].'</td>';
			echo '<td>'.$logData['user_identifier'].'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
echo '</table>';