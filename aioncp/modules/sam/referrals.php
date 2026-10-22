<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$ReferralSystem = new ReferralSystem();
$logsList = $ReferralSystem->getLogs();

if(is_array($logsList)) {
	
	echo '<table class="table">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Account</th>';
				echo '<th>Referred By</th>';
				echo '<th>Join Date</th>';
				echo '<th>Status</th>';
				echo '<th>Rewarded Date</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			
		foreach($logsList as $logData) {
			echo '<tr>';
				echo '<td>'.$logData['username'].'</td>';
				echo '<td>'.$logData['referral'].'</td>';
				echo '<td>'.$logData['join_date'].'</td>';
				echo '<td>'.($logData['status'] == 1 ? 'Completed!' : 'In Progress...').'</td>';
				echo '<td>'.($logData['status'] == 1 ? $logData['reward_date'] : '').'</td>';
			echo '</tr>';
		}
		echo '</tbody>';
	echo '</table>';

} else {
	message('There are no logs.', 'warning');
}