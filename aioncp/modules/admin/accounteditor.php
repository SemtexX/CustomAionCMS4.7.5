<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


message('This module edits the database directly without filtering input, use with precaution.', 'warning');

# manual form
if(check($_POST['account_submit'], $_POST['account_name'])) {
	redirect('admin/accounteditor/name/' . $_POST['account_name']);
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
					echo '<button type="submit" name="account_submit" value="ok" class="btn btn-primary">Search</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	
	echo '</div>';
echo '</div>';
	
if(check($_GET['name'])) {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		$disableEdit = array('id', 'name', 'password', 'last_server', 'last_ip', 'last_mac', 'last_hdd_serial', 'ip_force', 'question', 'answer', 'balance', 'hash', 'confirmed', 'cookie', 'registration_date', 'session_id');
		
		# edit value submit
		if(check($_POST['submit_edit'], $_POST['column_name'], $_POST['new_value'])) {
			try {
				
				$newValue = $_POST['new_value'];
				
				# changing email? lets check if already exists
				if($_POST['column_name'] == 'email') {
					
					$newValue = preg_replace('/[^A-Za-z0-9@.]/', '', $newValue); // clean var
					
					$searchEmail = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `email` = ?", array($newValue));
					if(is_array($searchEmail)) throw new Exception('The email <strong>'.$newValue.'</strong> is already being used (acc: '.$searchEmail['name'].')!');
				}
				
				$update = $db->query("UPDATE `account_data` SET ".$_POST['column_name']." = ? WHERE `id` = ?", array($newValue, $accountData['id']));
				if(!$update) throw new Exception('Could not update database.');
				
				message('Successfully set <strong>'.$_POST['column_name'].' = ' . $newValue . '</strong>', 'success');
				
				# if changed name, redirect to new name
				if($_POST['column_name'] == 'name') {
					redirect('admin/accounteditor/name/' . $newValue);
				}
				
				# load account data again
				$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['name']));
			} catch(Exception $ex) {
				message($ex->getMessage(), 'warning');
			}
		}
		
		debug($accountData);
		
		echo '<h4>Edit value:</h4>';
		
		# edit value form
		echo '<form class="form-inline" method="post">';
			echo '<div class="form-group">';
				echo '<select class="form-control" name="column_name">';
				foreach($accountData as $column => $data) {
					if(in_array($column, $disableEdit)) continue;
					echo '<option value="'.$column.'">'.$column.'</option>';
				}
				echo '</select>';
			echo '</div>';
			echo '<div class="form-group">';
				echo '<input type="text" class="form-control" name="new_value" placeholder="new value">';
			echo '</div>';
			echo '<button type="submit" name="submit_edit" value="ok" class="btn btn-primary">Edit</button>';
		echo '</form>';
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}