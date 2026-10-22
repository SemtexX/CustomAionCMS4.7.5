<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if(!check($_GET['name'])) {
	
	if(check($_POST['ban_submit'], $_POST['ban_account'])) {
		try {
			
			$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_POST['ban_account']));
			if(!is_array($accountData)) throw new Exception('Account does not exist.');
			if($accountData['ip_force'] == 1) throw new Exception('This account is already banned.');
			
			redirect('bans/new/name/' . $_POST['ban_account']);
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	echo '<div class="row">';
		echo '<div class="col-md-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Account</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="ban_account">';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<div class="col-sm-offset-2 col-sm-10">';
						echo '<button type="submit" name="ban_submit" value="ok" class="btn btn-primary">Ban Account</button>';
					echo '</div>';
				echo '</div>';
			echo '</form>';
		
		echo '</div>';
	echo '</div>';
	
} else {

	if(check($_POST['ban_complete'])) {
		try {
			if(!check($_POST['ban_account'])) throw new Exception("Invalid account.");
			if(!check($_POST['ban_reason'])) throw new Exception("Please type the reason of the ban.");
			if(!check($_POST['ban_duration'])) throw new Exception("Please select the duration of the ban.");
			
			# BAN ACCOUNT
			$banAccount = $db->query("UPDATE `account_data` SET `ip_force` = 1 WHERE `name` = ?", array($_POST['ban_account']));
			if(!$banAccount) throw new Exception('There was a system error, please contact Lau. [B1]');
			
			if($_POST['ban_duration'] == 'permanent') {
				
				# PERMANENT BAN
				$data = array(
					$_POST['ban_account'],
					$_SESSION['aioncp']['name'],
					$_POST['ban_reason'],
					$_POST['ban_proof'],
					time(),
					$_SESSION['aioncp']['name']
				);
				$query = "INSERT INTO permanent_bans (`account`, `staff`, `reason`, `proof`, `date`, `last_message_by`) VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), ?)";
				$ban = $gabs->query($query, $data);
				if(!$ban) throw new Exception("There was a system error, please contact Lau. [B2]");
				
				$banId = $gabs->lastInsertId();
				$banType = 1;
				$banTypeTxt = 'permanent';
				
			} else {
				
				# TEMPORAL BAN
				$data = array(
					$_POST['ban_account'],
					$_SESSION['aioncp']['name'],
					$_POST['ban_reason'],
					$_POST['ban_proof'],
					time(),
					time() + ($_POST['ban_duration']*3600),
					$_SESSION['aioncp']['name']
				);
				$query = "INSERT INTO temporal_bans (`account`, `staff`, `reason`, `proof`, `start_date`, `end_date`, `last_message_by`) VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?), ?)";
				$ban = $gabs->query($query, $data);
				if(!$ban) throw new Exception("There was a system error, please contact Lau. [B3]");
				
				$banId = $gabs->lastInsertId();
				$banType = 0;
				$banTypeTxt = 'temporal';
				
			}
			
			# BAN MESSAGE
			$messageData = array(
				$banId,
				time(),
				'ACTION: Account Banned',
				1,
				$banType,
				$_SESSION['aioncp']['name']
			);
			$banMessage = $gabs->query("INSERT INTO `messages` (`ban_id`, `date`, `message`, `type`, `ban_type`, `username`) VALUES (?, FROM_UNIXTIME(?), ?, ?, ?, ?)", $messageData);
			if(!$banMessage) throw new Exception("There was a system error, please contact Lau. [B4]");
			
			redirect('bans/view/type/'.$banTypeTxt.'/id/' . $banId);
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}

	echo '<div class="row">';
		echo '<div class="col-md-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<input type="hidden" name="ban_submit" value="ok"/>';
				echo '<input type="hidden" name="ban_account" value="'.$_GET['name'].'"/>';
				
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Account</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" value="'.$_GET['name'].'" disabled>';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<label for="input_1" class="col-sm-2 control-label">Reason</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_1" name="ban_reason" autofocus>';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<label for="input_2" class="col-sm-2 control-label">Proof</label>';
					echo '<div class="col-sm-10">';
						echo '<textarea class="form-control" id="input_2" rows="4" name="ban_proof"></textarea>';
					echo '</div>';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<label for="input_3" class="col-sm-2 control-label">Duration</label>';
					echo '<div class="col-sm-10">';
						echo '<select class="form-control" id="input_3" name="ban_duration">';
							echo '<option value="permanent">Permanent</option>';
							echo '<option value="1">1 Hour</option>';
							echo '<option value="2">2 Hours</option>';
							echo '<option value="3">3 Hours</option>';
							echo '<option value="4">4 Hours</option>';
							echo '<option value="5">5 Hours</option>';
							echo '<option value="6">6 Hours</option>';
							echo '<option value="7">7 Hours</option>';
							echo '<option value="8">8 Hours</option>';
							echo '<option value="9">9 Hours</option>';
							echo '<option value="10">10 Hours</option>';
							echo '<option value="11">11 Hours</option>';
							echo '<option value="12">12 Hours</option>';
							echo '<option value="24">1 Day</option>';
							echo '<option value="48">2 Days</option>';
							echo '<option value="72">3 Days</option>';
							echo '<option value="96">4 Days</option>';
							echo '<option value="120">5 Days</option>';
							echo '<option value="144">6 Days</option>';
							echo '<option value="168">7 Days</option>';
							echo '<option value="192">8 Days</option>';
							echo '<option value="216">9 Days</option>';
							echo '<option value="240">10 Days</option>';
							echo '<option value="264">11 Days</option>';
							echo '<option value="288">12 Days</option>';
							echo '<option value="312">13 Days</option>';
							echo '<option value="336">14 Days</option>';
							echo '<option value="360">15 Days</option>';
						echo '</select>';
					echo '</div>';
				echo '</div>';
				
				echo '<div class="form-group">';
					echo '<div class="col-sm-offset-2 col-sm-10">';
						echo '<button type="submit" name="ban_complete" value="ok" class="btn btn-danger">Ban Account</button>';
					echo '</div>';
				echo '</div>';
			echo '</form>';
		
		echo '</div>';
	echo '</div>';

}
