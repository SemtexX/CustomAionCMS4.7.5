<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


$aionDatabaseLink = "https://aioncodex.com/us/item/";

if(!check($_GET['name'])) {
	
	# manual form
	if(check($_POST['player_submit'], $_POST['player_name'])) {
		redirect('tools/playerdetails/name/' . $_POST['player_name']);
	}
	echo '<div class="row">';
		echo '<div class="col-lg-6">';
		
			echo '<form class="form-horizontal" method="post">';
				echo '<div class="form-group">';
					echo '<label for="input_0" class="col-sm-2 control-label">Player</label>';
					echo '<div class="col-sm-10">';
						echo '<input type="text" class="form-control" id="input_0" name="player_name" autofocus/>';
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
	
} else {
	
	try {
		
		if(!check($_GET['name'])) throw new Exception('Invalid request.');
		
		$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($_GET['name']));
		if(!is_array($playerData)) throw new Exception('Player name does not exist. (are you searching the correct server?)');
		
		$playerLegion = $sdb->queryFetchSingle("SELECT * FROM `legion_members` WHERE `player_id` = ?", array($playerData['id']));
		if(is_array($playerLegion)) {
			$legionData = $sdb->queryFetchSingle("SELECT * FROM `legions` WHERE `id` = ?", array($playerLegion['legion_id']));
		}
		//debug($playerData);
		
		echo '<div class="row">';
			echo '<div class="col-lg-4">';
			
				# PLAYER INFO
				echo '<div class="panel panel-success">';
					echo '<div class="panel-heading">'.$playerData['name'].'\'s Information</div>';
					echo '<div class="panel-body">';
						echo '<ul class="list-group">';
							echo '<li class="list-group-item"><strong>Player</strong><span class="pull-right text-muted small">'.$playerData['name'].'</span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Id</strong><span class="pull-right text-muted small">'.$playerData['id'].'</span></li>';
							echo '<li class="list-group-item"><strong>Account</strong><span class="pull-right text-muted small"><a href="'.__BASE_URL__.'tools/accountdata/name/'.$playerData['account_name'].'">'.$playerData['account_name'].'</a></span></li>';
							if(highRankAccess()) echo '<li class="list-group-item"><strong>Account Id</strong><span class="pull-right text-muted small">'.$playerData['account_id'].'</span></li>';
							echo '<li class="list-group-item"><strong>Level (exp)</strong><span class="pull-right text-muted small">'.expToLevel($playerData['exp']).' ('.number_format($playerData['exp']).')</span></li>';
							echo '<li class="list-group-item"><strong>Gender</strong><span class="pull-right text-muted small">'.$playerData['gender'].'</span></li>';
							echo '<li class="list-group-item"><strong>Race</strong><span class="pull-right text-muted small">'.$playerData['race'].'</span></li>';
							echo '<li class="list-group-item"><strong>Class</strong><span class="pull-right text-muted small">'.$playerData['player_class'].'</span></li>';
							if(is_array($legionData)) echo '<li class="list-group-item"><strong>Legion</strong><span class="pull-right text-muted small"><a href="'.__BASE_URL__.'tools/legiondetails/id/'.$legionData['id'].'/">'.$legionData['name'].'</a></span></li>';
							echo '<li class="list-group-item"><strong>Creation Date</strong><span class="pull-right text-muted small">'.$playerData['creation_date'].'</span></li>';
							echo '<li class="list-group-item"><strong>Deletion Date</strong><span class="pull-right text-muted small">'.$playerData['deletion_date'].'</span></li>';
							echo '<li class="list-group-item"><strong>Last Online</strong><span class="pull-right text-muted small">'.$playerData['last_online'].'</span></li>';
							echo '<li class="list-group-item"><strong>Status</strong><span class="pull-right text-muted small">'.($playerData['online'] == 1 ? '<span class="label label-success">Online</span>' : '<span class="label label-default">Offline</span>').'</span></li>';
						echo '</ul>';
						if(highRankAccess()) echo ' <a href="'.__BASE_URL__.'admin/playereditor/name/'.$playerData['name'].'/" class="btn btn-warning btn-xs">Edit</a>';
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
			echo '<div class="col-lg-8">';
				
				# EQUIPPED ITEMS
				echo '<div class="panel panel-info">';
					echo '<div class="panel-heading">Equipped Items</div>';
					echo '<div class="panel-body">';
						$equippedItems = $sdb->queryFetch("SELECT * FROM inventory WHERE item_owner = ? AND is_equiped = 1", array($playerData['id']));
						if(is_array($equippedItems)) {
							echo '<table class="table table-hover table-condensed">';
							echo '<thead>';
								echo '<tr>';
									echo '<th width="120px">Qty.</th>';
									echo '<th>Item Name</th>';
									if(highRankAccess()) echo '<th width="110px"></th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							foreach($equippedItems as $item) {
								$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($item['item_id']));
								$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
								echo '<tr>';
									echo '<td>'.number_format($item['item_count']).'</td>';
									echo '<td>';
										echo '<a id="'.$itemInfo['item_id'].'" class="aion-tooltip-text" href="'.$aionDatabaseLink.$itemInfo['item_id'].'" target="_blank">'.$itemName.'</a>';
										if($item['enchant'] >= 1) echo ' <span style="color:#ff0000;font-weight:bold;">+'.$item['enchant'].'</span>';
										if($item['tempering'] >= 1) echo ' <span style="color:#ffa200;font-weight:bold;">+'.$item['tempering'].'</span>';
									echo '</td>';
									if(highRankAccess()) echo '<td><a href="'.__BASE_URL__.'dev/itemeditor/uid/'.$item['item_unique_id'].'" class="btn btn-xs btn-warning">Edit</a></td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						} else {
							message('No items found.','warning');
						}
					echo '</div>';
				echo '</div>';
				
				# INVENTORY ITEMS
				echo '<div class="panel panel-info">';
					echo '<div class="panel-heading">Inventory</div>';
					echo '<div class="panel-body">';
						$inventoryItems = $sdb->queryFetch("SELECT * FROM inventory WHERE item_owner = ? AND is_equiped = 0", array($playerData['id']));
						if(is_array($inventoryItems)) {
							echo '<table class="table table-hover table-condensed">';
							echo '<thead>';
								echo '<tr>';
									echo '<th width="120px">Qty.</th>';
									echo '<th>Item Name</th>';
									if(highRankAccess()) echo '<th width="110px"></th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
							foreach($inventoryItems as $item) {
								$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($item['item_id']));
								$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
								
								echo '<tr>';
									echo '<td>'.number_format($item['item_count']).'</td>';
									echo '<td>';
										echo '<a id="'.$item['item_id'].'" class="aion-tooltip-text" href="'.$aionDatabaseLink.$item['item_id'].'" target="_blank">'.$itemName.'</a>';
										if($item['enchant'] >= 1) echo ' <span style="color:#ff0000;font-weight:bold;">+'.$item['enchant'].'</span>';
										if($item['tempering'] >= 1) echo ' <span style="color:#ffa200;font-weight:bold;">+'.$item['tempering'].'</span>';
									echo '</td>';
									if(highRankAccess()) echo '<td><a href="'.__BASE_URL__.'dev/itemeditor/uid/'.$item['item_unique_id'].'" class="btn btn-xs btn-warning">Edit</a></td>';
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						} else {
							message('No items found.','warning');
						}
					echo '</div>';
				echo '</div>';
				
			echo '</div>';
			
		echo '</div>';

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}