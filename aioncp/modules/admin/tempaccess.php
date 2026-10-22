<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(check($_POST['get_access'], $_POST['account_name'])) {
	try {
		
		# account data
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_POST['account_name']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		# check access level
		if($_SESSION['aioncp']['access_level'] != 100) {
			if($accountData['access_level'] > 0) throw new Exception('Access is denied to staff accounts.');
		}
		
		# check if already with temp pwd
		$checkTemp = $aioncp->queryFetchSingle("SELECT * FROM `temp_passwords` WHERE `name` = ?", array($accountData['name']));
		if(is_array($checkTemp)) throw new Exception('This account has an active temporal password, you must restore it in order to request a new one.');
		
		$currentPassword = $accountData['password'];
		$temporalPassword = 'tmp' . rand(1111111,9999999);
		$temporalPasswordCrypt = base64_encode(sha1($temporalPassword, true));
		
		# change password
		$changePassword = $db->query("UPDATE `account_data` SET `password` = ? WHERE `name` = ?", array($temporalPasswordCrypt, $accountData['name']));
		if(!$changePassword) throw new Exception('Could not complete request. [1]');
		
		# add data
		$addData = array(
			$accountData['name'],
			$currentPassword,
			$temporalPassword,
			$_SESSION['aioncp']['name']
		);
		
		# add to temp pwd
		$addTemp = $aioncp->query("INSERT INTO `temp_passwords` (`name`, `current_password`, `temp_password`, `temp_date`, `staff`) VALUES (?, ?, ?, now(), ?)", $addData);
		if(!$addTemp) {
			
			# restore old password
			$changePassword = $db->query("UPDATE `account_data` SET `password` = ? WHERE `name` = ?", array($currentPassword, $accountData['name']));
			if(!$changePassword) throw new Exception('Could not complete request. [2]');
			
			throw new Exception('Could not complete request. [3]');
		}
		
		# delete session from website
		$deleteSession = $db->query("DELETE FROM `aioncms`.`website_session_control` WHERE `userid` = ?", array($accountData['id']));
		
		message('Temporal access granted!', 'success');
		message($accountData['name'] . ' : <strong>' . $temporalPassword . '</strong>', 'warning');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

# RESTORE ACCOUNT
if(check($_GET['restore'], $_GET['account'])) {
	try {
		
		# account data
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['account']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		# check if already with temp pwd
		$checkTemp = $aioncp->queryFetchSingle("SELECT * FROM `temp_passwords` WHERE `name` = ?", array($accountData['name']));
		if(!is_array($checkTemp)) throw new Exception('This account does not have an active temporal password.');
		
		# restore password
		$changePassword = $db->query("UPDATE `account_data` SET `password` = ? WHERE `name` = ?", array($checkTemp['current_password'], $accountData['name']));
		if(!$changePassword) throw new Exception('Could not complete request. [1]');
		
		# remove from temp
		$removeTemp = $aioncp->query("DELETE FROM `temp_passwords` WHERE `name` = ?", array($accountData['name']));
		if(!$removeTemp) throw new Exception('Could not complete request. [2]');

		redirect(__BASE_URL__.'admin/tempaccess/');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';

	echo '<div class="col-md-8">';
		echo '<div class="block block-themed">';
			echo '<div class="block-header bg-primary">';
				echo '<h3 class="block-title">Account(s)</h3>';
			echo '</div>';
			echo '<div class="block-content">';
				
				$tempAccess = $aioncp->queryFetch("SELECT * FROM `temp_passwords` ORDER BY `temp_date` DESC");
				if(is_array($tempAccess)) {
					
					echo '<table class="table table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Account</th>';
							echo '<th>Current Password</th>';
							echo '<th>Temporal Password</th>';
							echo '<th>Access Granted</th>';
							echo '<th>Staff</th>';
							echo '<th></th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($tempAccess as $account) {
						echo '<tr>';
							echo '<td>'.$account['name'].'</td>';
							echo '<td>***********************</td>';
							echo '<td>'.$account['temp_password'].'</td>';
							echo '<td>'.$account['temp_date'].'</td>';
							echo '<td>'.$account['staff'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'admin/tempaccess/account/'.$account['name'].'/restore/1" class="btn btn-xs btn-danger">restore</a></td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
					
				} else {
					message('No active temporal requests.', 'warning');
				}
				
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	# RIGHT
	echo '<div class="col-md-4">';
		echo '<div class="block block-themed">';
			echo '<div class="block-header bg-primary">';
				echo '<h3 class="block-title">Request Access to Account</h3>';
			echo '</div>';
			echo '<div class="block-content">';
			
				echo '<form action="'.__BASE_URL__.'admin/tempaccess/" method="post">';
					echo '<div class="form-group">';
						echo '<input type="text" name="account_name" class="form-control" autofocus/>';
					echo '</div>';
					echo '<button type="submit" class="btn btn-success" name="get_access" value="ok">Request Access</button>';
				echo '</form><br />';
				
				echo '<p>After submitting the account you will be able to access it with an automatically generated temporal password. Once you have concluded your investigation remember to restore the account\'s original password.</p>';
				
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';