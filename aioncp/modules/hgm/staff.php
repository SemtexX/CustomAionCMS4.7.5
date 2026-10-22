<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


try {
	
	$staffAccounts = $db->queryFetch("SELECT * FROM `account_data` WHERE `access_level` > 0 ORDER BY `access_level` DESC, `id` ASC", array());
	if(!is_array($staffAccounts)) throw new Exception('No accounts found.');
	
	if(check($_GET['reset'])) {
		if($_SESSION['aioncp']['access_level'] >= 90) {
			foreach($staffAccounts as $row) {
				if($row['access_level'] >= 4) continue;
				$db->query("UPDATE `aioncms`.`players_onlinetime` SET `total_onlinetime` = 0 WHERE `account_id` = ?", array($row['id']));
			}
			redirect('hgm/staff/');
		}
	}
	
	function showStaffMembers($staffAccounts, $accessLevel=6) {
		
		$db = Handler::loadDB();
		$sdb = Handler::loadDB('siel');
		
		echo '<table class="table">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Account / Characters</th>';
				echo '<th>Online Time</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach($staffAccounts as $account) {
			if($account['access_level'] != $accessLevel) continue;
			
			$checkOnlineTime = $db->queryFetchSingle("SELECT * FROM `aioncms`.`players_onlinetime` WHERE `account_id` = ?", array($account['id']));
			
			if(is_array($checkOnlineTime)) {
				if($checkOnlineTime['total_onlinetime'] > 0) {
					$onlineTime = sec_to_hms($checkOnlineTime['total_onlinetime']);
				} else {
					$onlineTime = array(0, 0);
				}
			}
			
			echo '<tr>';
				echo '<td>';
					echo '<p class="font-w600 push-10"><a href="'.__BASE_URL__.'tools/accountdata/name/'.urlencode($account['name']).'">'.$account['name'].'</a></p>';
					echo '<p class="text-muted remove-margin-b">';
						$characters = $sdb->queryFetch("SELECT `name` FROM `players` WHERE `account_name` = ?", array($account['name']));
						if(is_array($characters)) {
							foreach($characters as $character) {
								echo '> <a href="'.__BASE_URL__.'tools/playerdetails/name/'.urlencode($character['name']).'">'.$character['name'].'</a><br />';
							}
						}
					echo '</p>';
				echo '</td>';
				
				echo '<td>'.$onlineTime[0].'<b>h</b> '.$onlineTime[1].'<b>m</b></td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-success">';
					echo '<h3 class="block-title">Admin</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 6);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-amethyst">';
					echo '<h3 class="block-title">Co-Admin</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 5);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-danger">';
					echo '<h3 class="block-title">H.GM</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 4);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-flat">';
					echo '<h3 class="block-title">S.AM</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 3);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-warning">';
					echo '<h3 class="block-title">AM</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 2);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<div class="block block-themed">';
				echo '<div class="block-header bg-primary-dark">';
					echo '<h3 class="block-title">Trainee</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					showStaffMembers($staffAccounts, 1);
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
			echo '<a href="'.__BASE_URL__.'hgm/staff/reset/1/" class="btn btn-primary">Reset Online Time (SAM, AM, TRAINEE)</a>';
		echo '</div>';
	echo '</div>';
	
	echo '<br /><br />';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}