<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


try {
	if(check($_GET['disable'], $_GET['user'])) {
		try {
			
			$checkAioncpAccount = $aioncp->queryFetchSingle("SELECT * FROM `account_access` WHERE `username` = ? AND `status` = 1", array($_GET['user']));
			if(!is_array($checkAioncpAccount)) throw new Exception('The user is not valid or it\'s already disabled.');
			
			if($checkAioncpAccount['access_level'] >= 90) throw new Exception('Admins cannot be disabled.');
			
			$disableAioncpAccount = $aioncp->query("UPDATE `account_access` SET `status` = 0 WHERE `username` = ?", array($_GET['user']));
			if(!$disableAioncpAccount) throw new Exception('There was an error, that\'s all I can say....');
			
			message('Account successfully disabled!','success');
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	if(check($_GET['enable'], $_GET['user'])) {
		try {
			
			$checkAioncpAccount = $aioncp->queryFetchSingle("SELECT * FROM `account_access` WHERE `username` = ? AND `status` = 0", array($_GET['user']));
			if(!is_array($checkAioncpAccount)) throw new Exception('The user is not valid or it\'s already enabled.');
			
			if($checkAioncpAccount['access_level'] >= 90) throw new Exception('Admins cannot be modified.');
			
			$enableAioncpAccount = $aioncp->query("UPDATE `account_access` SET `status` = 1 WHERE `username` = ?", array($_GET['user']));
			if(!$enableAioncpAccount) throw new Exception('There was an error, that\'s all I can say....');
			
			message('Account successfully enabled!','success');
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	$aioncpAccounts = $aioncp->queryFetch("SELECT * FROM `account_access`", array());
	if(!is_array($aioncpAccounts)) throw new Exception('There are no accounts.');
	
	echo '<div class="col-md-8">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				echo '<table class="table table-hover">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Account</th>';
						echo '<th>Name</th>';
						echo '<th>Email</th>';
						echo '<th>Access Level</th>';
						echo '<th>Status</th>';
						echo '<th></th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($aioncpAccounts as $account) {
					echo '<tr>';
						echo '<td>'.$account['username'].'</td>';
						echo '<td>'.$account['name'].'</td>';
						echo '<td>'.$account['email'].'</td>';
						echo '<td>'.($account['access_level'] > 100 ? '100' : $account['access_level']).'</td>';
						echo '<td>'.($account['status'] == 1 ? '<span class="label label-success">active</span>' : '<span class="label label-default">disabled</span>').'</td>';
						echo '<td>'.($account['status'] == 1 ? '<a href="'.__BASE_URL__.'admin/access/disable/1/user/'.$account['username'].'" class="btn btn-danger btn-xs">Disable</a>' : '<a href="'.__BASE_URL__.'admin/access/enable/1/user/'.$account['username'].'" class="btn btn-default btn-xs">Enable</a>').'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}