<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


if(!check($_GET['id'])) throw new Exception('You must provide a valid id.');

$shopItemData = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_items` WHERE `id` = ?", array($_GET['id']));
if(!is_array($shopItemData)) throw new Exception('You must provide a valid id.');

$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($shopItemData['item_id']));
$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
echo '<h1>'.$itemName.'</h1>';

$cList = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories`");
foreach($cList as $cData) {
	$categoriesList[$cData['id']] = $cData;
}
if(!is_array($categoriesList)) throw new Exception('There are no categories!');
if(!array_key_exists($shopItemData['category'], $categoriesList)) throw new Exception('The provided category id is not valid.');


if(check($_POST['item_edit'])) {
	try {
		
		if(!check($_POST['item_id'])) throw new Exception('You must provide a valid item id.');
		if(!check($_POST['item_count'])) throw new Exception('You must provide a valid item count.');
		if(!check($_POST['item_cost'])) throw new Exception('You must provide a valid item cost.');
		if(!check($_POST['item_enchantment'])) throw new Exception('You must provide a valid item enchantment.');
		if(!check($_POST['item_temperance'])) throw new Exception('You must provide a valid item temperance.');
		
		$customItemName = check($_POST['item_name']) ? $_POST['item_name'] : null;
		
		$addItem = $db->query("UPDATE `aioncms`.`website_shop_items` SET `item_id` = ?, `count` = ?, `cost` = ?, `name` = ?, `enchantment` = ?, `temperance` = ? WHERE `id` = ?", array($_POST['item_id'], $_POST['item_count'], $_POST['item_cost'], $customItemName, $_POST['item_enchantment'], $_POST['item_temperance'], $_GET['id']));
		if(!$addItem) throw new Exception('There was an error editing the item, try again.');
		
		redirect('webshop/items/category/' . $shopItemData['category']);
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';
	
	# RIGHT
	echo '<div class="col-md-4">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				
				echo '<form action="'.__BASE_URL__.'webshop/edit/category/'.$shopItemData['category'].'/id/'.$_GET['id'].'" method="post">';
						echo '<div class="form-group">';
							echo $categoriesList[$shopItemData['category']]['title'];
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Custom Item Name</label>';
							echo '<input type="text" name="item_name" class="form-control" value="'.$shopItemData['name'].'">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Item Id</label>';
							echo '<input type="text" name="item_id" class="form-control" value="'.$shopItemData['item_id'].'">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Count</label>';
							echo '<input type="text" name="item_count" class="form-control" value="'.$shopItemData['count'].'">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Enchantment</label>';
							echo '<input type="text" name="item_enchantment" class="form-control" value="'.$shopItemData['enchantment'].'">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Temperance</label>';
							echo '<input type="text" name="item_temperance" class="form-control" value="'.$shopItemData['temperance'].'">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Cost</label>';
							echo '<input type="text" name="item_cost" class="form-control" value="'.$shopItemData['cost'].'">';
						echo '</div>';
						echo '<button type="submit" class="btn btn-warning" name="item_edit" value="ok">Edit</button>';
					echo '</form><br />';
					
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';