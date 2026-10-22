<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>Ban System</h3>

<br /><br />

<?php
try {
	
	# filters
	if(!check($_GET['type'])) throw new Exception('Invalid request.');
	if(!check($_GET['id'])) throw new Exception('Invalid request.');
	if(!Validator::UnsignedNumber($_GET['id'])) throw new Exception('Invalid request.');
	if(!in_array($_GET['type'], array('permanent', 'temporal'))) throw new Exception('Invalid request.');
	
	# load database
	$db = Handler::loadDB();
	
	# get ban data
	if($_GET['type'] == 'permanent') {
		$banData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`permanent_bans` WHERE `id` = ? AND `account` = ?", array($_GET['id'], $_SESSION['username']));
		$banType = 1;
	} else {
		$banData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`temporal_bans` WHERE `id` = ? AND `account` = ?", array($_GET['id'], $_SESSION['username']));
		$banType = 0;
		$banDuration = sec_to_dhms(strtotime($banData['end_date'])-strtotime($banData['start_date']));
	}
	
	if(!is_array($banData)) throw new Exception('Could not load ban data.');
	
	echo '<div class="panel panel-primary">';
		echo '<div class="panel-heading">Ban Information</div>';
		echo '<div class="panel-body">';
			echo '<ul class="list-group">';
				echo '<li class="list-group-item"><strong>Ban ID</strong><span class="pull-right text-muted small" style="color:red;font-weight:bold;">#'.$banData['id'].'</span></li>';
				echo '<li class="list-group-item"><strong>Status</strong><span class="pull-right text-muted small">'.($banData['staff_lock'] == 0 ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Locked</span>').'</span></li>';
				echo '<li class="list-group-item"><strong>Issued By</strong><span class="pull-right text-muted small">'.$banData['staff'].'</span></li>';
				
				if($banType == 1) {
					echo '<li class="list-group-item"><strong>Date</strong><span class="pull-right text-muted small">'.date("Y-m-d h:i A", strtotime($banData['date'])).'</span></li>';
				} else {
					echo '<li class="list-group-item"><strong>Date</strong><span class="pull-right text-muted small">'.date("Y-m-d h:i A", strtotime($banData['start_date'])).'</span></li>';
					echo '<li class="list-group-item"><strong>Duration</strong><span class="pull-right text-muted small">'.$banDuration[0].' day(s) '.$banDuration[1].' hour(s)</span></li>';
				}
				echo '<li class="list-group-item"><strong>Reason</strong><span class="pull-right text-muted small">'.$banData['reason'].'</span></li>';
			echo '</ul>';
			
			echo '<div style="padding: 10px 15px;" style="word-wrap:break-word;">';
				echo '<strong>Proof:</strong><br />';
				echo nl2br($banData['proof']);
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	# submit ban message
	if(check($_POST['submit_message'], $_POST['message'])) {
		try {
			if(strlen($_POST['message']) < 3) throw new Exception('Your message is too short.'); // message too short
			if($banData['staff_lock'] == 1) throw new Exception('This ban is locked.'); // ban locked
			
			$messageData = array(
				$banData['id'],
				$_POST['message'],
				0,
				$banType,
				$_SESSION['username']
			);
			$addMessage = $db->query("INSERT INTO `aioncms`.`messages` (`ban_id`, `date`, `message`, `type`, `ban_type`, `username`) VALUES (?, now(), ?, ?, ?, ?)", $messageData);
			if(!$addMessage) throw new Exception('Could not send message.');
			
			if($banType == 1) {
				$updateBan = $db->query("UPDATE `aioncms`.`permanent_bans` SET `requires_action` = ?, `last_message_by` = ? WHERE `id` = ?", array(1, $_SESSION['username'], $banData['id']));
				if(!$updateBan) throw new Exception('Could not update ban.');
			} else {
				$updateBan = $db->query("UPDATE `aioncms`.`temporal_bans` SET `requires_action` = ?, `last_message_by` = ? WHERE `id` = ?", array(1, $_SESSION['username'], $banData['id']));
				if(!$updateBan) throw new Exception('Could not update ban.');
			}
			
			logSystem::add('replied to ban ('.$banData['id'].')');
			
			redirect($_GET['request']);
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
	
	# get ban messages
	$banMessages = $db->queryFetch("SELECT * FROM `aioncms`.`messages` WHERE `ban_id` = ? AND `ban_type` = ? ORDER BY `date` ASC", array($_GET['id'], $banType));
	
	if(is_array($banMessages)) {
		
		foreach($banMessages as $message) {
			
			if($message['type'] == 1) {
				# action
				message($message['message'] . '<span class="pull-right text-muted small">'.date("Y-m-d h:i A", strtotime($message['date'])).'</span>', 'error');
			} else {
				
				# reply
				if(strtolower($message['username']) != strtolower($_SESSION['username'])) {
					# staff message
					echo '<div class="panel panel-success">';
				} else {
					# user message
					echo '<div class="panel panel-default">';
				}
				
					echo '<div class="panel-heading"><strong>'.$message['username'].':</strong><span class="pull-right text-muted small">'.date("Y-m-d h:i A", strtotime($message['date'])).'</span></div>';
					echo '<div class="panel-body">';
						echo '<p style="word-wrap:break-word;">';
							echo nl2br($message['message']);
						echo '</p>';
					echo '</div>';
				echo '</div>';
			}
			
		}
		
	} else {
		message('There are no messages.', 'warning');
	}
	
	if($banData['staff_lock'] == 0) {
		echo '<div class="panel panel-default">';
			echo '<div class="panel-heading">Reply:</div>';
			echo '<div class="panel-body">';
				echo '<form action="'.module_url($_GET['request'], true).'" method="post">';
					echo '<textarea class="form-control" name="message" placeholder="Message..." style="height: 150px;"></textarea>';
					echo '<button type="submit" name="submit_message" value="ok" class="btn btn-primary" style="margin-top: 10px;">Send Message</button>';
				echo '</form>';
			echo '</div>';
		echo '</div>';
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}

?>