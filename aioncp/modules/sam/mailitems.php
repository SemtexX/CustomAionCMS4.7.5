<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$aionDatabaseLink = "http://aiondatabase.net/us/item/";
$excludeFromLogs = array();

if(check($_POST['submit_filter_date'], $_POST['filter_date1'], $_POST['filter_date2'])) {
	if(check($_POST['staffmember'])) {
		redirect(__BASE_URL__.'sam/mailitems/staffmember/' . $_POST['staffmember'] . '/from/' . $_POST['filter_date1'] . '/to/' . $_POST['filter_date2']);
	} else {
		redirect(__BASE_URL__.'sam/mailitems/from/' . $_POST['filter_date1'] . '/to/' . $_POST['filter_date2']);
	}
}

echo '<div class="row">';
	echo '<div class="col-md-6">';
		echo '<div class="block">';
			echo '<div class="block-content">';
			
			if(check($_POST['reward_submit'])) {
				try {
					if(!check($_POST['reward_player'])) throw new Exception('You must enter the player name.');
					if(!check($_POST['reward_itemid'])) throw new Exception('You must enter the item id.');
					if(!check($_POST['reward_qty'])) throw new Exception('You must enter the quantity.');
					if(!Validator::Number($_POST['reward_qty'], 1000, 1)) throw new Exception('Invalid item quantity.');
					
					# get player ID
					$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($_POST['reward_player']));
					if(!check($playerData)) throw new Exception("Player not found.");
					
					$playerId = $playerData['id'];
					if(!check($playerId)) throw new Exception("Player not found.");
					
					# Item ID
					$itemId = $_POST['reward_itemid'];
					
					# Item Count
					$qty = $_POST['reward_qty'];
					
					# Set activation count
					$activationCount = in_array($itemId, [188710162, 188710163, 188710164, 164005001, 164005002, 164005003, 164005004, 164005005]) ? 10000000 : 1;
					
					# Send Item
					//$sendItem = $sdb->query("INSERT INTO `web_reward` (`item_owner`, `item_id`, `item_count`) VALUES (?, ?, ?)", array($playerId, $itemId, $qty));
					//if(!$sendItem) throw new Exception("OMG you broke it! x.x");
					
					/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
					/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
					
					// GET LAST INVENTORY ID
					$inventoryLastId = $sdb->queryFetchSingle("SELECT MAX(item_unique_id) as result FROM `inventory`");
					if(!is_array($inventoryLastId)) throw new Exception('Error getting last item id.');
					$uniqueItemId = $inventoryLastId['result']+1;
					
					// INSERT INVENTORY ITEM
					$inventoryItemData = array(
						'item_unique_id' => $uniqueItemId,
						'item_id' => $itemId,
						'item_skin' => $itemId,
						'item_count' => $qty,
						'item_owner' => $playerId,
						'item_creator' => 'GlobalXcentra Admin',
						'activation_count' => $activationCount, // Set activation count to 999 for specific item IDs
						'item_location' => 127,
						'enchant' => 0,
						'authorize' => 0
					);
					$addInventoryItem = $sdb->query("INSERT INTO `inventory` (item_unique_id, item_id, item_skin, item_count, item_owner, item_creator, activation_count, item_location, enchant, authorize) VALUES (:item_unique_id, :item_id, :item_skin, :item_count, :item_owner, :item_creator, :activation_count, :item_location, :enchant, :authorize)", $inventoryItemData);
					if(!$addInventoryItem) throw new Exception('Error adding items to inventory.');
					
					// GET MAIL LAST ID
					$mailLastId = $sdb->queryFetchSingle("SELECT MAX(mail_unique_id) as result FROM `mail`");
					if(!is_array($mailLastId)) {
						$mailLastId['result'] = 0;
					}
					
					// INSERT MAIL
					$mailData = array(
						'mail_unique_id'		=>	$mailLastId['result'] + 1,
						'mail_recipient_id'		=>	$playerId,
						'sender_name'			=>	'Admin',
						'mail_title'			=>	'Webshop Delivery',
						'mail_message'			=>	'Thank you for purchasing.',
						'unread'				=>	1,
						'attached_item_id'		=>	$uniqueItemId,
						'attached_kinah_count'	=>	0,
						'express'				=>	1
					);
					$addMail = $sdb->query("INSERT INTO `mail` (mail_unique_id, mail_recipient_id, sender_name, mail_title, mail_message, unread, attached_item_id, attached_kinah_count, express) VALUES (:mail_unique_id, :mail_recipient_id, :sender_name, :mail_title, :mail_message, :unread, :attached_item_id, :attached_kinah_count, :express)", $mailData);
					if(!$addMail) throw new Exception('Error adding mail.');
					
					/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
					/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
					
					# Save Log
					if(!in_array($_SESSION['aioncp']['name'], $excludeFromLogs)) {
						$logData = array(
							'by' => $_SESSION['aioncp']['name'],
							'ip' => $_SERVER['REMOTE_ADDR'],
							'acc' => $playerData['account_name'],
							'player' => $playerData['name'],
							'item' => $itemId,
							'qty' => $qty
						);
						
						$saveLog = $aioncp->query("INSERT INTO `mail_logs` (`sent_by`, `sent_by_ip`, `username`, `playername`, `itemid`, `qty`, `date`) VALUES (:by, :ip, :acc, :player, :item, :qty, now())", $logData);
					}
					
					message('Item successfully mailed.', 'success');
					
				} catch(Exception $ex) {
					message($ex->getMessage(), 'error');
				}
			}
				
			echo '<form class="form-inline" method="post">';
				echo '<div class="form-group">';
					echo '<input type="text" class="form-control" name="reward_player" placeholder="Player Name" autofocus>&nbsp;';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<input type="text" class="form-control" name="reward_itemid" placeholder="Item Id">&nbsp;';
				echo '</div>';
				echo '<div class="form-group">';
					echo '<select class="form-control" name="reward_qty">';
						echo '<option value="1">1</option>';
						echo '<option value="3">3</option>';
						echo '<option value="5">5</option>';
						echo '<option value="10">10</option>';
						echo '<option value="15">15</option>';
						echo '<option value="20">20</option>';
						echo '<option value="25">25</option>';
						echo '<option value="50">50</option>';
						echo '<option value="100">100</option>';
						echo '<option value="200">200</option>';
						echo '<option value="300">300</option>';
						echo '<option value="400">400</option>';
						echo '<option value="500">500</option>';
						echo '<option value="600">600</option>';
						echo '<option value="700">700</option>';
						echo '<option value="800">800</option>';
						echo '<option value="900">900</option>';
						echo '<option value="1000">1000</option>';
					echo '</select>&nbsp;';
				echo '</div>';
				echo '<button type="submit" name="reward_submit" value="ok" class="btn btn-primary">Mail</button>';
			echo '</form><br />';
				
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	# RIGHT
	echo '<div class="col-md-6">';
		echo '<div class="block">';
				
			echo '<ul class="nav nav-tabs" data-toggle="tabs">';
				echo '<li class="active">';
					echo '<a href="#btabs-static-staff">Filter by Staff</a>';
				echo '</li>';
				echo '<li class="">';
					echo '<a href="#btabs-static-date">Filter by Date</a>';
				echo '</li>';
			echo '</ul>';
			echo '<div class="block-content tab-content">';
				echo '<div class="tab-pane active" id="btabs-static-staff">';
					// FILTER BY NAME
					$logsStaff = $aioncp->queryFetch("SELECT DISTINCT(`sent_by`) FROM `mail_logs`");
					if(is_array($logsStaff)) {
						foreach($logsStaff as $k => $staff) {
							echo '<a href="'.__BASE_URL__.'sam/mailitems/staffmember/'.urlencode($staff['sent_by']).'">'.$staff['sent_by'].'</a>';
							if($k != (count($logsStaff)-1)) echo ', ';
						}
					}
					
					echo '<br />';
					echo '<br />';
					
				echo '</div>';
				echo '<div class="tab-pane" id="btabs-static-date">';
					// FILTER BY DATE
					echo '<form action="'.__BASE_URL__.'sam/mailitems/" method="post">';
						if(check($_GET['staffmember'])) echo '<input type="hidden" name="staffmember" value="'.$_GET['staffmember'].'"/>';
						echo '<div class="input-daterange input-group" data-date-format="yyyy-mm-dd">';
							echo '<input class="form-control" type="text" id="filter-daterange1" name="filter_date1" placeholder="From">';
							echo '<span class="input-group-addon"><i class="fa fa-chevron-right"></i></span>';
							echo '<input class="form-control" type="text" id="filter-daterange2" name="filter_date2" placeholder="To">';
						echo '</div><br /><button class="btn btn-primary" type="submit" name="submit_filter_date" value="ok">Filter</button><br /><br />';
					echo '</form>';
					
				echo '</div>';
			echo '</div>';
					
		echo '</div>';
	echo '</div>';
	
echo '</div>';

if(check($_GET['from'])) {
	
	// DATE SEARCH
	try {
		
		$from = strtotime($_GET['from']);
		
		if(check($_GET['to'])) {
			$to = strtotime($_GET['to']);
		} else {
			$to = time();
		}
		
		if(!Validator::UnsignedNumber($from)) throw new Exception('Invalid request.');
		if(!Validator::UnsignedNumber($to)) throw new Exception('Invalid request.');
		if($from > $to) throw new Exception('Invalid request.');
		if($to-$from > 432000) throw new Exception('Can\'t display more than 5 days worth of logs!');
		
		$fromDate = date("Y-m-d 00:00:00", $from);
		$toDate = date("Y-m-d 00:00:00", $to);
		
		# SEARCH RESULTS
		echo '<div class="row">';

			echo '<div class="col-md-12">';
				echo '<div class="block">';
					echo '<div class="block-header bg-smooth-dark">';
						echo '<h3 class="block-title text-white">Logs between '.$fromDate.' - '.$toDate.' '.(check($_GET['staffmember']) ? '('.$_GET['staffmember'].')' : '(All Staff Members)').'</h3>';
					echo '</div>';
					echo '<div class="block-content">';
						
						if(check($_GET['staffmember'])) {
							$logs = $aioncp->queryFetch("SELECT * FROM `mail_logs` WHERE `sent_by` = ? AND `date` BETWEEN ? AND ? ORDER BY `date` ASC", array($_GET['staffmember'], $fromDate, $toDate));
							if(!is_array($logs)) throw new Exception('No results found.');
						} else {
							$logs = $aioncp->queryFetch("SELECT * FROM `mail_logs` WHERE `date` BETWEEN ? AND ? ORDER BY `date` ASC", array($fromDate, $toDate));
							if(!is_array($logs)) throw new Exception('No results found.');
						}
						
						$previousRowDay = 0;
						
						if(is_array($logs)) {
						
							echo '<table class="table table-hover table-condensed">';
							echo '<thead>';
								echo '<tr>';
									echo '<th>Date</th>';
									echo '<th>Sent By</th>';
									echo '<th>IP</th>';
									echo '<th>Player\'s Account</th>';
									echo '<th>Player Name</th>';
									echo '<th>Item</th>';
									echo '<th>Qty</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							foreach($logs as $row) {
								
								$itemData = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['itemid']));
								
								if($previousRowDay != date("Y-m-d", strtotime($row['date']))) {
									echo '<tr class="info">';
										echo '<td colspan="8" style="font-weight:bold;font-size:18px;"><br /><br />'.date("Y-m-d", strtotime($row['date'])).'</td>';
									echo '</tr>';
								}
								
								echo '<tr>';
									echo '<td>'.$row['date'].'</td>';
									echo '<td>'.$row['sent_by'].'</td>';
									echo '<td><a href="'.__BASE_URL__.'sam/ipsearch/address/'.urlencode($row['sent_by_ip']).'">'.$row['sent_by_ip'].'</a></td>';
									echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['username'].'">'.$row['username'].'</a></td>';
									echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.urlencode($row['playername']).'">'.$row['playername'].'</a></td>';
									echo '<td>[<a href="'.$aionDatabaseLink.$row['itemid'].'" target="_blank">'.$row['itemid'].'</a>]';
										if(is_array($itemData)) {
											echo ' ' . $itemData['item_name'];
										}
									echo '</td>';
									echo '<td>'.$row['qty'].'</td>';
								echo '</tr>';
								
								$previousRowDay = date("Y-m-d", strtotime($row['date']));
							}
							echo '</tbody>';
							echo '</table>';
						
						}
						
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		echo '</div>';
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
	
} else {

	# LAST 100 LOGS
	echo '<div class="row">';

		echo '<div class="col-md-12">';
			echo '<div class="block">';
				echo '<div class="block-header bg-smooth-dark">';
					echo '<h3 class="block-title text-white">Last 100 Logs</h3>';
				echo '</div>';
				echo '<div class="block-content">';
					
					if(check($_GET['staffmember'])) {
						$logs = $aioncp->queryFetch("SELECT * FROM `mail_logs` WHERE `sent_by` = ? ORDER BY `date` DESC LIMIT 1000", array($_GET['staffmember']));
						if(!is_array($logs)) die('No results found.');
					} else {
						$logs = $aioncp->queryFetch("SELECT * FROM `mail_logs` ORDER BY `date` DESC LIMIT 100");
					}
					
					if(is_array($logs)) {
					
						echo '<table class="table table-hover table-condensed">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Date</th>';
								echo '<th>Sent By</th>';
								echo '<th>IP</th>';
								echo '<th>Player\'s Account</th>';
								echo '<th>Player Name</th>';
								echo '<th>Item</th>';
								echo '<th>Qty</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($logs as $row) {
							
							$itemData = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['itemid']));
							
							echo '<tr>';
								echo '<td>'.$row['date'].'</td>';
								echo '<td>'.$row['sent_by'].'</td>';
								echo '<td><a href="'.__BASE_URL__.'sam/ipsearch/address/'.urlencode($row['sent_by_ip']).'">'.$row['sent_by_ip'].'</a></td>';
								echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['username'].'">'.$row['username'].'</a></td>';
								echo '<td><a href="'.__BASE_URL__.'tools/playerdetails/name/'.urlencode($row['playername']).'">'.$row['playername'].'</a></td>';
								echo '<td>[<a href="'.$aionDatabaseLink.$row['itemid'].'" target="_blank">'.$row['itemid'].'</a>]';
									if(is_array($itemData)) {
										echo ' ' . $itemData['item_name'];
									}
								echo '</td>';
								echo '<td>'.$row['qty'].'</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					
					}
					
				echo '</div>';
			echo '</div>';
		echo '</div>';
		
	echo '</div>';

}