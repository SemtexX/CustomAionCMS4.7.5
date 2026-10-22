<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(check($_POST['unstick_submit'])) {
	try {
		if(!check($_POST['unstick_input'])) throw new Exception('Incomplete request.');
		if(!check($_POST['unstick_type'])) throw new Exception('Incomplete request.');
		
		switch($_POST['unstick_type']) {
			case 'account':
				$characters = $sdb->queryFetch("SELECT `name` FROM `players` WHERE `account_name` = ?", array($_POST['unstick_input']));
				if(!is_array($characters)) throw new Exception("Account has no characters.");
				
				foreach($characters as $character) {
					$unstick = $sdb->query("UPDATE `players` SET `world_id` = '400010000', `x` = '1513.58', `y` = '1324.69', `z` = '1528.17', `online` = '0' WHERE `name` = ?", array($character['name']));
					if(!$unstick) throw new Exception("The request could not be completed.");
				}
				break;
			case 'character':
				$unstick = $sdb->query("UPDATE `players` SET `world_id` = '400010000', `x` = '1513.58', `y` = '1324.69', `z` = '1528.17', `online` = '0' WHERE `name` = ?", array($_POST['unstick_input']));
				if(!$unstick) throw new Exception("The request could not be completed.");
				break;
			default:
				throw new Exception('Invalid request.');
		}
		
		message('Character / Account successfully unstuck!', 'success');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';
	echo '<div class="col-md-6">';
	
		echo '<form class="form-horizontal" method="post">';
			echo '<div class="form-group">';
				echo '<label for="input_0" class="col-sm-2 control-label">Request</label>';
				echo '<div class="col-sm-10">';
					echo '<input type="text" class="form-control" id="input_0" name="unstick_input">';
				echo '</div>';
			echo '</div>';

			echo '<div class="form-group">';
			echo '<div class="col-sm-offset-2 col-sm-10">';
				echo '<div class="radio">';
					echo '<label>';
						echo '<input type="radio" name="unstick_type" id="r1" value="character" checked>';
						echo 'Character';
					echo '</label>';
				echo '</div>';
				echo '<div class="radio">';
					echo '<label>';
						echo '<input type="radio" name="unstick_type" id="r2" value="account">';
						echo 'Account (all characters)';
					echo '</label>';
				echo '</div>';
			echo '</div>';
			echo '</div>';

			echo '<div class="form-group">';
				echo '<div class="col-sm-offset-2 col-sm-10">';
					echo '<button type="submit" name="unstick_submit" value="ok" class="btn btn-primary">Unstick</button>';
				echo '</div>';
			echo '</div>';
		echo '</form>';
	
	echo '</div>';
echo '</div>';