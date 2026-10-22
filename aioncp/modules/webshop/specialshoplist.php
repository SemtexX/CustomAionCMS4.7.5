<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

// ADD ITEM
if(check($_POST['item_add'])) {
	try {
		# filters
		if(!check($_POST['item_id'])) throw new Exception('You didn\'t enter a valid item id.');
		if(!Validator::UnsignedNumber($_POST['item_id'])) throw new Exception('You didn\'t enter a valid item id.');
		
		# check if already in the weekly shop
		$isInSpecialShop = $db->queryFetchSingle("SELECT * FROM `aioncms`.`weeklyspecial_itemlist` WHERE `item_id` = ?", array($_POST['item_id']));
		if(is_array($isInSpecialShop)) throw new Exception('The item you\'re trying to add is already in the weekly shop.');
		
		# check if item is valid
		$isSupported = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($_POST['item_id']));
		if(!is_array($isSupported)) throw new Exception('The item you\'re trying to add doesn\'t seem to be supported in Gamez yet or it\'s not valid.');
		
		# add item
		$addItem = $db->query("INSERT INTO `aioncms`.`weeklyspecial_itemlist` (`item_id`, `cost`, `chance`) VALUES (?, ?, ?)", array($_POST['item_id'], 0, 0));
		if(!$addItem) throw new Exception('The item could not be added to the database, try again.');
		
		message('<strong>'.$isSupported['item_name'].'</strong> added to the weekly special shop!', 'success');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

// DELETE ITEM
if(check($_GET['delete'])) {
	try {
		if(!Validator::UnsignedNumber($_GET['delete'])) throw new Exception('The item id is not valid.');
		
		$deleteItem = $db->query("DELETE FROM `aioncms`.`weeklyspecial_itemlist` WHERE `item_id` = ?", array($_GET['delete']));
		if(!$deleteItem)  throw new Exception('The item could not be deleted from the database.');
		
		message('Item removed from the weekly special shop.', 'success');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}


echo '<div class="row">';
	echo '<div class="col-md-8">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				// ITEM LIST
				$result = $db->queryFetch("SELECT * FROM `aioncms`.`weeklyspecial_itemlist` ORDER BY `id` DESC", array());
				
				echo '<form action="'.__BASE_URL__.'webshop/specialshoplist/" method="post">';
				echo '<table class="table table-hover">';
				echo '<thead>';
					echo '<tr>';
						echo '<th></th>';
						echo '<th>Id</th>';
						echo '<th>Item Name</th>';
						echo '<th>Base Price</th>';
						//echo '<th>Limit</th>';
						echo '<th>Qty</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					foreach($result as $row) {
						
						if(check($_POST['edit_items'])) {
							$newCost = $_POST['cost_' . $row['item_id']];
							$newLimit = $_POST['limit_' . $row['item_id']];
							$newQty = $_POST['qty_' . $row['item_id']];
							if($row['cost'] != $newCost || $row['limit'] != $newLimit || $row['qty'] != $newQty) {
								$modifiedItems[] = array($row['item_id'], $newCost, $newLimit, $newQty);
							}
						}
						
						$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ? ", array($row['item_id']));
						$itemName = (check($itemInfo['item_name']) ? $itemInfo['item_name'] : 'Unknown');
						
						echo '<tr>';
							echo '<td><a href="'.__BASE_URL__.'webshop/specialshoplist/delete/'.$row['item_id'].'" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></a></td>';
							echo '<td>'.$row['item_id'].'</td>';
							echo '<td>'.$itemName.'</td>';
							echo '<td><input type="text" name="cost_'.$row['item_id'].'" class="form-control" value="'.$row['cost'].'" style="width: 80px;"></td>';
							//echo '<td><input type="text" name="limit_'.$row['item_id'].'" class="form-control" value="'.$row['limit'].'" style="width: 50px;"></td>';
							echo '<input type="hidden" name="limit_'.$row['item_id'].'" value="'.$row['limit'].'"/>';
							echo '<td><input type="text" name="qty_'.$row['item_id'].'" class="form-control" value="'.$row['qty'].'" style="width: 50px;"></td>';
						echo '</tr>';
					}
					echo '<tr>';
						echo '<td colspan="4"><button type="submit" name="edit_items" value="ok" class="btn btn-success">Save Changes</button></td>';
					echo '</tr>';
				echo '</tbody>';
				echo '</table>';
				echo '</form>';
				
				// EDIT ITEM
				if(check($_POST['edit_items'])) {
					try {
						if(is_array($modifiedItems)) {
							foreach($modifiedItems as $editItem) {
								$edit = $db->query("UPDATE `aioncms`.`weeklyspecial_itemlist` SET `cost` = ?, `limit` = ?, `qty` = ? WHERE `item_id` = ?", array($editItem[1], $editItem[2], $editItem[3], $editItem[0]));
								if(!$edit) throw new Exception('There was an error while editing the item, try again.');
							}
							redirect('webshop/specialshoplist/');
						}
					} catch(Exception $ex) {
						message($ex->getMessage(), 'error');
					}
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	# RIGHT
	echo '<div class="col-md-4">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				
				echo '<form action="'.__BASE_URL__.'webshop/specialshoplist/" method="post">';
						echo '<div class="form-group">';
							echo '<input type="text" name="item_id" class="form-control" placeholder="1234567890">';
						echo '</div>';
						echo '<button type="submit" class="btn btn-success" name="item_add" value="ok">Add Item</button>';
					echo '</form><br />';
					
					echo '<p>After submitting the item id you will be able to set its price and chance of appearing.</p>';
					
					//echo '<p><strong>Limit:</strong> Maximum amount of purchases for a weekly shop. Set to 0 to use system\'s limit.</p>';
					echo '<p><strong>QTY:</strong> Amount of items to be mailed (stackable items only!)</p>';
					
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';