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
		redirect('tools/accountdata/name/' . $_POST['account_name']);
	}
	echo '<div class="row">';
		echo '<div class="col-lg-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Account</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="account_name" autofocus/>';
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
	
} else {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$accountData = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($accountData)) throw new Exception('Account does not exist.');
		
		$accountSecurity = $db->queryFetchSingle("SELECT * FROM `account_security` WHERE `id` = ?", array($accountData['id']));
		
		//$accountLog = $db->queryFetch("SELECT * FROM `accounts_log` WHERE `account_id` = ? ORDER BY `id` ASC", array($accountData['id']));
		
		# get latest ban
		if($accountData['ip_force'] == 1) {
			$banButton = '<span class="label label-danger">Banned</span>';
			
			# check permanent bans
			$permanentBan = $gabs->queryFetchSingle("SELECT * FROM `permanent_bans` WHERE `permanent_bans`.`account` = ? ORDER BY `id` DESC", array($accountData['name']));
			if(is_array($permanentBan)) {
				$lastBanType = 'permanent';
				$lastBanId = $permanentBan['id'];
				$banButton = '<a href="'.__BASE_URL__.'bans/view/type/permanent/id/'.$lastBanId.'" class="btn btn-xs btn-danger" target="_blank">Permanent Ban #'.$lastBanId.'</a>';
			}
			# check temporal bans
			$temporalBan = $gabs->queryFetchSingle("SELECT * FROM `temporal_bans` WHERE `temporal_bans`.`account` = ? ORDER BY `id` DESC", array($accountData['name']));
			if(is_array($temporalBan)) {
				if(is_array($permanentBan)) {
					if(strtotime($temporalBan['start_date']) > strtotime($permanentBan['date'])) {
						$lastBanType = 'temporal';
						$lastBanId = $temporalBan['id'];
						$banButton = '<a href="'.__BASE_URL__.'bans/view/type/temporal/id/'.$lastBanId.'" class="btn btn-xs btn-warning" target="_blank">Temporal Ban #'.$lastBanId.'</a>';
					}
				} else {
					$lastBanType = 'temporal';
					$lastBanId = $temporalBan['id'];
					$banButton = '<a href="'.__BASE_URL__.'bans/view/type/temporal/id/'.$lastBanId.'" class="btn btn-xs btn-warning" target="_blank">Temporal Ban #'.$lastBanId.'</a>';
				}
			}
		}
		
		# membership type
		if($accountData['membership'] > 0) {
			if($accountData['membership'] == 1) {
				# premium
				$membershipType = 'Premium';
			} else {
				# vip
				$membershipType = 'VIP';
				$vipVxpiration = $accountData['expire'];
			}
		} else {
			$membershipType = 'Regular';
		}
		
		# old membership type
		if($accountData['old_membership'] == 1) {
			$oldMembershipType = 'Premium';
		} else {
			$oldMembershipType = 'Regular';
		}
		
		echo '<div class="row">';
			echo '<div class="col-lg-4">';
			
				# ACCOUNT INFO
				echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading">Account Information</div>';
					echo '<div class="panel-body">';
						echo '<ul class="list-group">';
							echo '<li class="list-group-item"><strong>Account</strong><span class="pull-right text-muted small">'.$accountData['name'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Id</strong><span class="pull-right text-muted small">'.$accountData['id'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Activated</strong><span class="pull-right text-muted small">'.$accountData['activated'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Access Level</strong><span class="pull-right text-muted small">'.$accountData['access_level'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Last IP</strong><span class="pull-right text-muted small"><a href="'.__BASE_URL__.'hgm/ipsearch/address/'.urlencode($accountData['last_ip']).'">'.$accountData['last_ip'].'</a></span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Last MAC Address</strong><span class="pull-right text-muted small"><a href="'.__BASE_URL__.'hgm/macsearch/address/'.urlencode($accountData['last_mac']).'">'.$accountData['last_mac'].'</a></span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Last HDD Serial</strong><span class="pull-right text-muted small">'.$accountData['last_hdd_serial'].'</span></li>';
							echo '<li class="list-group-item"><strong>Banned</strong><span class="pull-right text-muted small">'.($accountData['ip_force'] == 1 ? $banButton : '<span class="label label-success">Active</span>').'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Email Address</strong><span class="pull-right text-muted small">'.$accountData['email'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Credits</strong><span class="pull-right text-muted small">'.number_format($accountData['credits']).'</span></li>';
							echo '<li class="list-group-item"><strong>Membership</strong><span class="pull-right text-muted small">'.$membershipType.'</span></li>';
							echo '<li class="list-group-item"><strong>Old Membership</strong><span class="pull-right text-muted small">'.$oldMembershipType.'</span></li>';
							if(check($vipVxpiration)) echo '<li class="list-group-item"><strong>VIP Expiration</strong><span class="pull-right text-muted small">'.$vipVxpiration.'</span></li>';
							echo '<li class="list-group-item"><strong>Registration Date</strong><span class="pull-right text-muted small">'.(check($accountData['created_at']) ? date("Y-m-d h:i A", strtotime($accountData['created_at'])) : '<em>none</em>').'</span></li>';
						echo '</ul>';
						
						if(is_array($accountSecurity)) {
						echo '<ul class="list-group">';
							echo '<li class="list-group-item"><strong>Email Confirmed</strong><span class="pull-right text-muted small">'.($accountSecurity['email_confirmed'] == 1 ? '<span class="label label-success">Yes</span>' : '<span class="label label-default">No</span>').'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Q.1</strong><span class="pull-right text-muted small" data-toggle="tooltip" data-placement="top" title="" type="button" data-original-title="'.$accountSecurity['answer_1'].'">'.$accountSecurity['question_1'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Q.2</strong><span class="pull-right text-muted small" data-toggle="tooltip" data-placement="top" title="" type="button" data-original-title="'.$accountSecurity['answer_2'].'">'.$accountSecurity['question_2'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>PIN</strong><span class="pull-right text-muted small">'.$accountSecurity['security_pin'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>VC</strong><span class="pull-right text-muted small">'.md5($accountData['name'] . md5('validateUser3000')).'</span></li>';
							echo '<li class="list-group-item"><strong>Account Locked</strong><span class="pull-right text-muted small">'.($accountData['allowed_hdd_serial'] == 'locked' ? '<span class="label label-success">Locked</span>' : '<span class="label label-default">Unlocked</span>').'</span></li>';
						echo '</ul>';
						}
						
						if($accountData['ip_force'] != 1) echo '<a href="'.__BASE_URL__.'bans/new/name/'.$accountData['name'].'/" class="btn btn-danger btn-xs">Ban Account</a>';
						if(highRankAccess()) echo ' <a href="'.__BASE_URL__.'admin/accounteditor/name/'.$accountData['name'].'/" class="btn btn-warning btn-xs">Edit</a>';
					echo '</div>';
				echo '</div>';
				
				# CHARACTERS LIST
				$characters = $sdb->queryFetch("SELECT `name`,`online` FROM `players` WHERE `account_name` = ?", array($accountData['name']));
				echo '<div class="panel panel-primary">';
					echo '<div class="panel-heading">Character(s)</div>';
					echo '<div class="panel-body">';
						if(is_array($characters)) {
							echo '<table class="table table-condensed">';
								foreach($characters as $char) {
									echo '<tr>';
										echo '<td>';
										echo '<a href="'.__BASE_URL__.'tools/playerdetails/name/'.urlencode($char['name']).'">'.$char['name'].'</a>';
										if($char['online'] == 1) echo '<span class="pull-right text-muted small"><span class="label label-success">online</span></span>';
										echo '</td>';
									echo '</tr>';
								}
							echo '</table>';
						} else {
							message('No characters found.', 'warning');
						}
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
			# LOG LIST
			echo '<div class="col-lg-8">';
			
				# VOTE LOGS
				$voteLogs = $db->queryFetch("SELECT * FROM `aioncms`.`votes` WHERE `name` = ? ORDER BY `newdate` DESC", array($accountData['name']));
				if(is_array($voteLogs)) {
					echo '<div class="panel panel-primary">';
						echo '<div class="panel-heading">Vote Log</div>';
						echo '<div class="panel-body">';
							echo '<div class="table-responsive">';
							echo '<table class="table table-hover table-condensed">';
								echo '<thead>';
								echo '<tr>';
									echo '<th>vote unlock</th>';
									if(highRankAccess()) echo '<th>ip</th>';
									if(highRankAccess()) echo '<th>mac</th>';
									echo '<th class="hidden-xs">site</th>';
								echo '</tr>';
								echo '</thead>';
							echo '<tbody>';
							foreach($voteLogs as $row) {
								echo '<tr>';
									echo '<td>'.date("Y-m-d H:i:s", $row['newdate']).'</td>';
									if(highRankAccess()) echo '<td><a href="'.__BASE_URL__.'hgm/ipsearch/address/'.urlencode($row['ip']).'">'.$row['ip'].'</a></td>';
									if(highRankAccess()) echo '<td><a href="'.__BASE_URL__.'hgm/macsearch/address/'.urlencode($row['mac']).'">'.$row['mac'].'</a></td>';
									echo '<td class="hidden-xs">'.$row['site'].'</td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}
				
			echo '</div>';
			
		echo '</div>';

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}