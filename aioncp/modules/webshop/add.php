<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


if(!check($_GET['category'])) throw new Exception('You must provide a category id.');


$cList = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories`");
foreach($cList as $cData) {
	$categoriesList[$cData['id']] = $cData;
}
if(!is_array($categoriesList)) throw new Exception('There are no categories!');
if(!array_key_exists($_GET['category'], $categoriesList)) throw new Exception('The provided category id is not valid.');


if(check($_POST['item_add'])) {
	try {
		
		if(!check($_POST['item_id'])) throw new Exception('You must provide a valid item id.');
		if(!check($_POST['item_count'])) throw new Exception('You must provide a valid item count.');
		if(!check($_POST['item_cost'])) throw new Exception('You must provide a valid item cost.');
		if(!check($_POST['item_enchantment'])) throw new Exception('You must provide a valid item enchantment.');
		if(!check($_POST['item_temperance'])) throw new Exception('You must provide a valid item temperance.');
		
		$addItem = $db->query("INSERT INTO `aioncms`.`website_shop_items` (`item_id`, `count`, `cost`, `category`, `enchantment`, `temperance`) VALUES (?, ?, ?, ?, ?, ?)", array($_POST['item_id'], $_POST['item_count'], $_POST['item_cost'], $_GET['category'], $_POST['item_enchantment'], $_POST['item_temperance']));
		if(!$addItem) throw new Exception('There was an error adding the item, try again.');
		
		redirect('webshop/items/category/' . $_GET['category']);
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';
	
	# RIGHT
	echo '<div class="col-md-4">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				
				echo '<form action="'.__BASE_URL__.'webshop/add/category/'.$_GET['category'].'" method="post">';
						echo '<div class="form-group">';
							echo $categoriesList[$_GET['category']]['title'];
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Item Id</label>';
							echo '<input type="text" name="item_id" class="form-control" placeholder="Item id">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Item Count</label>';
							echo '<input type="text" name="item_count" class="form-control" value="1">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Enchantment</label>';
							echo '<input type="text" name="item_enchantment" class="form-control" value="0">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Temperance</label>';
							echo '<input type="text" name="item_temperance" class="form-control" value="0">';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<label>Cost</label>';
							echo '<input type="text" name="item_cost" class="form-control" placeholder="Item cost">';
						echo '</div>';
						echo '<button type="submit" class="btn btn-success" name="item_add" value="ok">Add</button>';
					echo '</form><br />';
					
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';