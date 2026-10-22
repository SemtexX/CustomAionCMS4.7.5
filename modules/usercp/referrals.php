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

<h3>Referral System</h3>
<p>Invite your friends to play at Aion and receive a reward of <strong><?php echo number_format(config('referral_credits_reward')); ?> credits</strong> for each referred friend who reaches <strong><?php echo config('referral_required_onlinetime_hours'); ?> hours</strong> of in-game online time!</p>
<br /><br />

<h4>My Referral Link</h4>
<p>Share this link with all your friends!</p>
<input type="text" class="form-control" value="<?php echo config('referral_link_base') . $_SESSION['userid']; ?>" readonly/>
<br /><br />


<h4>My Referrals</h4>
<?php
try {
	
	$ReferralSystem = new ReferralSystem();
	$ReferralSystem->setUsername($_SESSION['username']);
	
	$referrals = $ReferralSystem->getAccountReferrals();
	if(!is_array($referrals)) throw new Exception('You have not referred any friends yet u.u');
	
	echo '<table class="table table-striped table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Friend</th>';
			echo '<th>Join Date</th>';
			echo '<th>Status</th>';
			echo '<th>Reward Date</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($referrals as $row) {
		
		echo '<tr>';
			echo '<td>'.$row['username'].'</td>';
			echo '<td>'.$row['join_date'].'</td>';
			echo '<td>'.($row['status'] == 1 ? 'Completed!' : 'In Progress...').'</td>';
			echo '<td>'.($row['status'] == 1 ? $row['reward_date'] : '').'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'warning');
}
?>