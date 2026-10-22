<?php
// Am Anfang jeder Seite
include('countdown_timer.php');
?>
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>My Redeemed Codes</h3>
<p>List of codes you have already redeemed.</p>
<br /><br />

<?php
try {
	
	$RedeemCode = new RedeemCode();
	$RedeemCode->setUser($_SESSION['username']);
	
	$redeemLogs = $RedeemCode->getUserLogs();
	if(!is_array($redeemLogs)) throw new Exception('You have not redeemed any codes yet.');
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Date</th>';
			echo '<th>Code</th>';
			echo '<th>Reward</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($redeemLogs as $row) {
		echo '<tr>';
			echo '<td>'.$row['date_redeemed'].'</td>';
			echo '<td>'.$row['redeem_code'].'</td>';
			echo '<td>'.number_format($row['redeem_credit_amount']).' credit(s)</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'warning');
}
?>