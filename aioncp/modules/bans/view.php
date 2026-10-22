<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	if(!check($_GET['id'])) redirect($_SESSION['aioncp']['last_location']);
	if(!check($_GET['type'])) redirect($_SESSION['aioncp']['last_location']);

	if($_GET['type'] == 'permanent') {
		$banData = $gabs->queryFetchSingle("SELECT * FROM `permanent_bans` WHERE `id` = ?", array($_GET['id']));
		$type = 1;
		$tableName = 'permanent_bans';
	} else {
		$banData = $gabs->queryFetchSingle("SELECT * FROM `temporal_bans` WHERE `id` = ?", array($_GET['id']));
		$type = 0;
		$tableName = 'temporal_bans';
	}
	
	if(!is_array($banData)) throw new Exception("Could not load ban data.");
	
	// BAN REPLY
	if(check($_POST['reply_ban'], $_POST['reply_mesage'])) {
		try {
			if($banData['staff_lock'] == 1) throw new Exception('This ban is locked, you must unlock it first!');
			
			$newMessageData = array(
				$banData['id'],
				$_POST['reply_mesage'],
				0,
				$type,
				$_SESSION['aioncp']['name'],
			);
			$newMessage = $gabs->query("INSERT INTO `messages` (`ban_id`, `message`, `type`, `ban_type`, `username`, `date`) VALUES (?, ?, ?, ?, ?, now())", $newMessageData);
			if(!$newMessage) throw new Exception('Could not add reply to the database.');
			
			$updateBan = $gabs->query("UPDATE `".$tableName."` SET `last_message_by` = ?, `requires_action` = ? WHERE `id` = ?", array($_SESSION['aioncp']['name'], 0, $banData['id']));
			if(!$updateBan) throw new Exception('Could not update ban information.');
			redirect('bans/view/type/'.$_GET['type'].'/id/' . $banData['id']);
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	// BAN ACTIONS
	if(check($_GET['action'])) {
		try {
			switch($_GET['action']) {
				case 'lock':
					if($banData['staff_lock'] == 1) throw new Exception('This ban is already locked.');
					$gabs->query("INSERT INTO `messages` (`ban_id`, `message`, `type`, `ban_type`, `username`, `date`) VALUES (?, ?, ?, ?, ?, now())", array($banData['id'], 'ACTION: Ban Locked', 1, $type, $_SESSION['aioncp']['name']));
					$gabs->query("UPDATE `".$tableName."` SET `last_message_by` = ?, `staff_lock` = ?, `user_lock` = ?, `requires_action` = ? WHERE `id` = ?", array($_SESSION['aioncp']['name'], 1, 1, 0, $banData['id']));
					//$gabs->query("DELETE FROM `escalated_bans` WHERE `ban_id` = ?", array($banData['id']));
					redirect('bans/view/type/'.$_GET['type'].'/id/' . $banData['id']);
					break;
				case 'unlock':
					if($banData['staff_lock'] != 1) throw new Exception('This ban is already open.');
					$gabs->query("INSERT INTO `messages` (`ban_id`, `message`, `type`, `ban_type`, `username`, `date`) VALUES (?, ?, ?, ?, ?, now())", array($banData['id'], 'ACTION: Ban Unlocked', 1, $type, $_SESSION['aioncp']['name']));
					$gabs->query("UPDATE `".$tableName."` SET `last_message_by` = ?, `staff_lock` = ?, `user_lock` = ?, `requires_action` = ? WHERE `id` = ?", array($_SESSION['aioncp']['name'], 0, 0, 1, $banData['id']));
					//$gabs->query("DELETE FROM `escalated_bans` WHERE `ban_id` = ?", array($banData['id']));
					redirect('bans/view/type/'.$_GET['type'].'/id/' . $banData['id']);
					break;
				// case 'escalate':
					// if($banData['staff_lock'] == 1) throw new Exception('This ban is locked, you must unlock it first!');
					// $gabs->query("INSERT INTO `messages` (`ban_id`, `message`, `type`, `ban_type`, `username`, `date`) VALUES (?, ?, ?, ?, ?, now())", array($banData['id'], 'ACTION: Ban Escalated to HGM/Admins', 1, $type, $_SESSION['aioncp']['name']));
					// $gabs->query("UPDATE `".$tableName."` SET `last_message_by` = ?, `requires_action` = ? WHERE `id` = ?", array($_SESSION['aioncp']['name'], 0, $banData['id']));
					// $gabs->query("INSERT INTO `escalated_bans` (`ban_id`, `staff`, `ban_type`, `date`) VALUES (?, ?, ?, now())", array($banData['id'], $_SESSION['aioncp']['name'], $type));
					// redirect('bans/view/type/'.$_GET['type'].'/id/' . $banData['id']);
					// break;
				case 'lift':
					unbanAccount($banData['account']);
					$gabs->query("INSERT INTO `messages` (`ban_id`, `message`, `type`, `ban_type`, `username`, `date`) VALUES (?, ?, ?, ?, ?, now())", array($banData['id'], 'ACTION: Ban Lifted', 1, $type, $_SESSION['aioncp']['name']));
					$gabs->query("UPDATE `".$tableName."` SET `last_message_by` = ?, `staff_lock` = ?, `user_lock` = ?, `requires_action` = ? WHERE `id` = ?", array($_SESSION['aioncp']['name'], 1, 1, 0, $banData['id']));
					//$gabs->query("DELETE FROM `escalated_bans` WHERE `ban_id` = ?", array($banData['id']));
					redirect('bans/view/type/'.$_GET['type'].'/id/' . $banData['id']);
					break;
				default:
					throw new Exception('Not a valid ban action.');
			}
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}

	echo '<div class="row">';
		# general info
		echo '<div class="col-md-6">';
			
			echo '<div class="block">';
				echo '<div class="block-content">';
					$banMessages = $gabs->queryFetch("SELECT * FROM `messages` WHERE `ban_id` = ? AND `ban_type` = ? ORDER BY `id` ASC", array($banData['id'], $type));
					if(is_array($banMessages)) {
						foreach($banMessages as $message) {
							if($message['type'] == 1) {
								echo '<div class="panel panel-danger">';
									echo '<div class="panel-heading">'.$message['message'].' ('.$message['date'].')</div>';
							} else {
								if(strtolower($message['username']) == strtolower($banData['account'])) {
									echo '<div class="panel panel-default">';
								} else {
									echo '<div class="panel panel-success">';
								}
									echo '<div class="panel-heading">Message by <strong>'.$message['username'].'</strong> ('.$message['date'].')</div>';
									echo '<div class="panel-body">';
										echo nl2br($message['message']);
									echo '</div>';
							}
							echo '</div>';
						}
					} else {
						message('No messages found.', 'warning');
					}
					
					// reply
					echo '<form method="post">';
						echo '<div class="form-group">';
							echo '<textarea class="form-control" id="input_0" rows="6" name="reply_mesage"></textarea>';
						echo '</div>';
						echo '<button type="submit" name="reply_ban" value="ok" class="btn btn-primary">Submit Reply</button>';
					echo '</form><br />';
			
				echo '</div>';
			echo '</div>';
			
		echo '</div>';
		
		# proof
		echo '<div class="col-md-6">';
			
			echo '<div class="panel panel-info">';
				echo '<div class="panel-heading">General Ban Information</div>';
				echo '<div class="panel-body">';
					echo '<ul>';
						echo '<li><strong>Ban ID</strong>: <span style="color:red;font-weight:bold;">#'.$banData['id'].'</span></li>';
						echo '<li><strong>Account</strong>: <a href="'.__BASE_URL__.'tools/accountdata/name/'.$banData['account'].'">'.$banData['account'].'</a></li>';
						echo '<li><strong>Status</strong>: '.($banData['staff_lock'] == 1 ? 'locked' : 'open').'</li>';
						echo '<li><strong>Banned By</strong>: '.$banData['staff'].'</li>';
						if($_GET['type'] == 'permanent') echo '<li><strong>Ban Date</strong>: '.$banData['date'].'</li>';
						if($_GET['type'] == 'temporal') echo '<li><strong>Ban Start Date</strong>: '.$banData['start_date'].'</li>';
						if($_GET['type'] == 'temporal') echo '<li><strong>Ban End Date</strong>: '.$banData['end_date'].'</li>';
						echo '<li><strong>Reason</strong>: '.$banData['reason'].'</li>';
					echo '</ul>';
				echo '</div>';
			echo '</div>';
			
			echo '<div class="panel panel-info">';
				echo '<div class="panel-heading">Ban Proof</div>';
				echo '<div class="panel-body">';
					echo nl2br($banData['proof']);
				echo '</div>';
			echo '</div>';
			
			if($banData['staff_lock'] != 1) echo '<a href="'.__BASE_URL__.'bans/view/type/'.$_GET['type'].'/id/'.$banData['id'].'/action/lock" class="btn btn-danger">Lock Ban</a> ';
			if($banData['staff_lock'] == 1) echo '<a href="'.__BASE_URL__.'bans/view/type/'.$_GET['type'].'/id/'.$banData['id'].'/action/unlock" class="btn btn-default">Unlock Ban</a> ';
			//if($banData['staff_lock'] != 1) echo '<a href="'.__BASE_URL__.'bans/view/type/'.$_GET['type'].'/id/'.$banData['id'].'/action/escalate" class="btn btn-warning">Escalate Ban</a> ';
			echo '<a href="'.__BASE_URL__.'bans/view/type/'.$_GET['type'].'/id/'.$banData['id'].'/action/lift" class="btn btn-success">Lift Ban</a>';
			
		echo '</div>';
		
	echo '</div>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}