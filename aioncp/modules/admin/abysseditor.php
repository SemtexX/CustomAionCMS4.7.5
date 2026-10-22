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
if(check($_POST['player_submit'], $_POST['player_name'])) {
	redirect('admin/abysseditor/name/' . $_POST['player_name']);
}
echo '<div class="row">';
	echo '<div class="col-md-6">';
	
		echo '<form class="form-horizontal" method="post">';
			echo '<div class="form-group">';
				echo '<label for="input_0" class="col-sm-2 control-label">Player Name</label>';
				echo '<div class="col-sm-10">';
					echo '<input type="text" class="form-control" id="input_0" name="player_name">';
				echo '</div>';
			echo '</div>';

			echo '<div class="form-group">';
				echo '<div class="col-sm-offset-2 col-sm-10">';
					echo '<button type="submit" name="player_submit" value="ok" class="btn btn-primary">Search</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	
	echo '</div>';
echo '</div>';
	
if(check($_GET['name'])) {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($playerData)) throw new Exception('Player name does not exist. (are you searching the correct server?)');
		
		$abyssData = $sdb->queryFetchSingle("SELECT * FROM `abyss_rank` WHERE `abyss_rank`.`player_id` = ?", array($playerData['id']));
		if(!is_array($abyssData)) throw new Exception('No abyss data found.');
		
		$disableEdit = array('player_id', 'top_ranking', 'last_update');
		
		# edit value submit
		if(check($_POST['submit_edit'], $_POST['column_name'], $_POST['new_value'])) {
			try {
				if($playerData['online'] == 1) throw new Exception('The player cannot be edited while he\'s online!');
				
				$update = $sdb->query("UPDATE `abyss_rank` SET ".$_POST['column_name']." = ? WHERE `player_id` = ?", array($_POST['new_value'], $abyssData['player_id']));
				if(!$update) throw new Exception('Could not update database.');
				
				message('Successfully set <strong>'.$_POST['column_name'].' = ' . $_POST['new_value'] . '</strong>', 'success');
				
				# load player data again
				$abyssData = $sdb->queryFetchSingle("SELECT * FROM `abyss_rank` WHERE `abyss_rank`.`player_id` = ?", array($playerData['id']));
			} catch(Exception $ex) {
				message($ex->getMessage(), 'warning');
			}
		}
		
		debug($abyssData);
		
		echo '<h4>Edit value:</h4>';
		
		# edit value form
		echo '<form class="form-inline" method="post">';
			echo '<div class="form-group">';
				echo '<select class="form-control" name="column_name">';
				foreach($abyssData as $column => $data) {
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