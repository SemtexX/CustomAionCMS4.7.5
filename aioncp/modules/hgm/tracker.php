<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	# TRACKING COUNT
	$trackCount = $db->queryFetchSingle("SELECT COUNT(*) as total FROM `aioncms`.`item_tracker_list`", array());
	echo 'Currently tracking: <span style="color:red;font-weight: bold;">'.number_format($trackCount['total']).'</span> items.';
	echo '<br /><br />';
	
	# SEARCH FORM
	echo '<form class="form-inline" method="post">';
		echo '<div class="form-group">';
			echo '<select class="form-control" name="searchtype">';
				echo '<option value="playername">Player Name</option>';
				echo '<option value="playernamedel">Player Name (deleted)</option>';
				echo '<option value="uniqueid">Unique Item ID</option>';
			echo '</select>';
		echo '</div>&nbsp;';
		echo '<div class="form-group">';
			echo '<input type="text" class="form-control" name="searchvalue">';
		echo '</div>&nbsp;';
		echo '<button type="submit" class="btn btn-primary">Search</button>';
	echo '</form><br /><br />';
	
	# SEARCH FORM SUBMITTED
	if(check($_POST['searchtype'], $_POST['searchvalue'])) {
		switch($_POST['searchtype']) {
			case "playername":
				redirect('hgm/tracker/name/' . $_POST['searchvalue']);
				break;
			case "playernamedel":
				redirect('hgm/tracker/deleted/yes/name/' . $_POST['searchvalue']);
				break;
			case "uniqueid":
				redirect('hgm/tracker/uid/' . $_POST['searchvalue']);
				break;
			default:
				redirect('hgm/tracker/');
			
		}
	}
	
	
	if(check($_GET['uid'])) {
		
		# SHOW LOGS FOR SINGLE ITEM
		$trackerLogs = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` WHERE `item_unique_id` = ? ORDER BY `id` DESC LIMIT 50", array($_GET['uid']));
		
		if(is_array($trackerLogs)) {
			
			echo '<table class="table table-condensed">';
				echo '<thead>';
					echo '<tr>';
						//echo '<th>#</th>';
						echo '<th>UID</th>';
						echo '<th>Item</th>';
						echo '<th>Old Owner</th>';
						echo '<th>New Owner</th>';
						echo '<th>Log Date</th>';
						//echo '<th>Log Type</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($trackerLogs as $row) {
					
					$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['item_id']));
					$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
					
					$oldOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['old_owner']));
					if(is_array($oldOwner)) {
						$oldOwnerName = $oldOwner['name'];
					} else {
						if(check($row['old_owner_name'])) {
							$oldOwnerName = '<span style="color:red;">' . $row['old_owner_name'] . '</span>';
						} else {
							$oldOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					$newOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['new_owner']));
					if(is_array($newOwner)) {
						$newOwnerName = $newOwner['name'];
					} else {
						if(check($row['new_owner_name'])) {
							$newOwnerName = '<span style="color:red;">' . $row['new_owner_name'] . '</span>';
						} else {
							$newOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					
					echo '<tr>';
						//echo '<td>'.$row['id'].'</td>';
						echo '<td><a href="'.__BASE_URL__.'hgm/tracker/uid/'.$row['item_unique_id'].'">'.$row['item_unique_id'].'</a></td>';
						echo '<td>'.$itemName.'</td>';
						echo '<td>'.(check($row['old_owner_account']) ? '<a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['old_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($oldOwnerName).'">'.$oldOwnerName.'</a></td>';
						echo '<td>'.(check($row['new_owner_account']) ? ' <a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['new_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($newOwnerName).'">'.$newOwnerName.'</a></td>';
						echo '<td>'.$row['log_date'].'</td>';
						//echo '<td>Trade</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
			
			echo '<br />';
			echo '<a href="'.__BASE_URL__.'hgm/tracker" class="btn btn-info">Back</a>';
			
		}
		
	} elseif(check($_GET['name'])) {
		# SHOW LOGS FOR PLAYER NAME
		
		
		if(check($_GET['deleted'])) {
			# SEARCH BY NAME (WILL INCLUDE SAVED CHARACTER NAMES -- THE ONES DELETED)
			$trackerLogsOut = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` WHERE `old_owner_name` = ? ORDER BY `id` DESC", array($_GET['name']));
			$trackerLogsIn = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` WHERE `new_owner_name` = ? ORDER BY `id` DESC", array($_GET['name']));
			
			echo '<h2 style="color:blue;">'.$_GET['name'].'\'s Logs</h2>';
			
		} else {
			# REGULAR SEARCH BY CHARACTER ID (GETS THE ID FIRST)
			$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ?", array($_GET['name']));
			if(!is_array($playerData)) throw new Exception("Invalid player name.");
			
			$trackerLogsOut = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` WHERE `old_owner` = ? ORDER BY `id` DESC", array($playerData['id']));
			$trackerLogsIn = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` WHERE `new_owner` = ? ORDER BY `id` DESC", array($playerData['id']));
			
			echo '<h2 style="color:blue;">'.$playerData['name'].'\'s Logs</h2>';
		}
		
		# OUT
		if(is_array($trackerLogsOut)) {
			echo '<h4>Outgoing Items</h4>';
			echo '<table class="table table-condensed">';
				echo '<thead>';
					echo '<tr>';
						//echo '<th>#</th>';
						echo '<th>UID</th>';
						echo '<th>Item</th>';
						echo '<th>Old Owner</th>';
						echo '<th>New Owner</th>';
						echo '<th>Log Date</th>';
						//echo '<th>Log Type</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($trackerLogsOut as $row) {
					
					$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['item_id']));
					$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
					
					$oldOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['old_owner']));
					if(is_array($oldOwner)) {
						$oldOwnerName = $oldOwner['name'];
					} else {
						if(check($row['old_owner_name'])) {
							$oldOwnerName = '<span style="color:red;">' . $row['old_owner_name'] . '</span>';
						} else {
							$oldOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					$newOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['new_owner']));
					if(is_array($newOwner)) {
						$newOwnerName = $newOwner['name'];
					} else {
						if(check($row['new_owner_name'])) {
							$newOwnerName = '<span style="color:red;">' . $row['new_owner_name'] . '</span>';
						} else {
							$newOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					echo '<tr>';
						//echo '<td>'.$row['id'].'</td>';
						echo '<td><a href="'.__BASE_URL__.'hgm/tracker/uid/'.$row['item_unique_id'].'">'.$row['item_unique_id'].'</a></td>';
						echo '<td>'.$itemName.'</td>';
						echo '<td>'.(check($row['old_owner_account']) ? '<a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['old_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($oldOwnerName).'">'.$oldOwnerName.'</a></td>';
						echo '<td>'.(check($row['new_owner_account']) ? ' <a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['new_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($newOwnerName).'">'.$newOwnerName.'</a></td>';
						echo '<td>'.$row['log_date'].'</td>';
						//echo '<td>Trade</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
		}
		
		# IN
		if(is_array($trackerLogsIn)) {
			echo '<h4>Incoming Items</h4>';
			echo '<table class="table table-condensed">';
				echo '<thead>';
					echo '<tr>';
						//echo '<th>#</th>';
						echo '<th>UID</th>';
						echo '<th>Item</th>';
						echo '<th>Old Owner</th>';
						echo '<th>New Owner</th>';
						echo '<th>Log Date</th>';
						//echo '<th>Log Type</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($trackerLogsIn as $row) {
					
					$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['item_id']));
					$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
					
					$oldOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['old_owner']));
					if(is_array($oldOwner)) {
						$oldOwnerName = $oldOwner['name'];
					} else {
						if(check($row['old_owner_name'])) {
							$oldOwnerName = '<span style="color:red;">' . $row['old_owner_name'] . '</span>';
						} else {
							$oldOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					$newOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['new_owner']));
					if(is_array($newOwner)) {
						$newOwnerName = $newOwner['name'];
					} else {
						if(check($row['new_owner_name'])) {
							$newOwnerName = '<span style="color:red;">' . $row['new_owner_name'] . '</span>';
						} else {
							$newOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					echo '<tr>';
						//echo '<td>'.$row['id'].'</td>';
						echo '<td><a href="'.__BASE_URL__.'hgm/tracker/uid/'.$row['item_unique_id'].'">'.$row['item_unique_id'].'</a></td>';
						echo '<td>'.$itemName.'</td>';
						echo '<td>'.(check($row['old_owner_account']) ? '<a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['old_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($oldOwnerName).'">'.$oldOwnerName.'</a></td>';
						echo '<td>'.(check($row['new_owner_account']) ? ' <a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['new_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($newOwnerName).'">'.$newOwnerName.'</a></td>';
						echo '<td>'.$row['log_date'].'</td>';
						//echo '<td>Trade</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
		}
		
		echo '<br />';
		echo '<a href="'.__BASE_URL__.'hgm/tracker" class="btn btn-info">Back</a>';

		
		
	} else {
		
		# SHOW LAST 50 LOGS
		$trackerLogs = $db->queryFetch("SELECT * FROM `aioncms`.`item_tracker_logs` ORDER BY `id` DESC LIMIT 50", array());
		if(is_array($trackerLogs)) {
			
			echo '<table class="table table-condensed">';
				echo '<thead>';
					echo '<tr>';
						//echo '<th>#</th>';
						echo '<th>UID</th>';
						echo '<th>Item</th>';
						echo '<th>Old Owner</th>';
						echo '<th>New Owner</th>';
						echo '<th>Log Date</th>';
						//echo '<th>Log Type</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($trackerLogs as $row) {
					
					$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($row['item_id']));
					$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
					
					$oldOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['old_owner']));
					if(is_array($oldOwner)) {
						$oldOwnerName = $oldOwner['name'];
					} else {
						if(check($row['old_owner_name'])) {
							$oldOwnerName = '<span style="color:red;">' . $row['old_owner_name'] . '</span>';
						} else {
							$oldOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					$newOwner = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `id` = ?", array($row['new_owner']));
					if(is_array($newOwner)) {
						$newOwnerName = $newOwner['name'];
					} else {
						if(check($row['new_owner_name'])) {
							$newOwnerName = '<span style="color:red;">' . $row['new_owner_name'] . '</span>';
						} else {
							$newOwnerName = '<span style="color:red;">unknown</span>';
						}
					}
					
					echo '<tr>';
						//echo '<td>'.$row['id'].'</td>';
						echo '<td><a href="'.__BASE_URL__.'hgm/tracker/uid/'.$row['item_unique_id'].'">'.$row['item_unique_id'].'</a></td>';
						echo '<td>'.$itemName.'</td>';
						echo '<td>'.(check($row['old_owner_account']) ? '<a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['old_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($oldOwnerName).'">'.$oldOwnerName.'</a></td>';
						echo '<td>'.(check($row['new_owner_account']) ? ' <a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['new_owner_account'].'">[A]</a>&nbsp;&nbsp;' : NULL).'<a href="'.__BASE_URL__.'hgm/tracker/name/'.strip_tags($newOwnerName).'">'.$newOwnerName.'</a></td>';
						echo '<td>'.$row['log_date'].'</td>';
						//echo '<td>Trade</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
			
		}
		
	}

} catch(Exception $ex) {
	debug($ex->getMessage());
}