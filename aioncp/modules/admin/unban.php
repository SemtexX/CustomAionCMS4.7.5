<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(!check($_GET['name'])) {
	
	# manual form
	if(check($_POST['account_submit'], $_POST['account_name'])) {
		redirect('admin/unban/name/' . $_POST['account_name']);
	}
	echo '<div class="row">';
		echo '<div class="col-md-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Account</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="account_name">';
					echo '</div>';
				echo '</div>';

				echo '<div class="form-group">';
					echo '<div class="col-sm-offset-2 col-sm-10">';
						echo '<button type="submit" name="account_submit" value="ok" class="btn btn-primary">Unban</button>';
					echo '</div>';
				echo '</div>';
			echo '</form>';
		
		echo '</div>';
	echo '</div>';
	
} else {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		if($accountData['ip_force'] != 1) throw new Exception('The account is not banned.');
		
		$banAccount = unbanAccount($_GET['name']);
		if(!$banAccount) throw new Exception('There was an error updating the database.');
		
		message('Account unbanned!', 'success');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}