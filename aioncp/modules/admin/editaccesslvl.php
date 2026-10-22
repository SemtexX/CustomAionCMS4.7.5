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
	if(check($_POST['account_submit'], $_POST['account_name'], $_POST['access_level'])) {
		redirect('admin/editaccesslvl/name/' . $_POST['account_name'] . '/level/' . $_POST['access_level']);
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
					echo '<label for="input_1" class="col-sm-2 control-label">Access Level</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_1" name="access_level">';
					echo '</div>';
				echo '</div>';

				echo '<div class="form-group">';
					echo '<div class="col-sm-offset-2 col-sm-10">';
						echo '<button type="submit" name="account_submit" value="ok" class="btn btn-primary">Change</button>';
					echo '</div>';
				echo '</div>';
			echo '</form>';
		
		echo '</div>';
	echo '</div>';
	
} else {
	
	try {
		
		if(!check($_GET['name'], $_GET['level'])) throw new Exception('Invalid request.');
		if(!Validator::UnsignedNumber($_GET['level'])) throw new Exception('Invalid access level.');
		if(!Validator::Number($_GET['level'], 6, 0)) throw new Exception('Invalid access level.');
		
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		$changeLevel = $db->query("UPDATE `account_data` SET `access_level` = ? WHERE `name` = ?", array($_GET['level'], $_GET['name']));
		if(!$changeLevel) throw new Exception('There was an error updating the database.');
		
		message('Access level updated!', 'success');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}