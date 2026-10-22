<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


if(check($_GET['delete'])) {
	$deleteItem = $db->query("DELETE FROM `aioncms`.`website_shop_items` WHERE `id` = ?", array($_GET['delete']));
	redirect('webshop/items/category/' . $_GET['category']);
}

$cList = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories`");
foreach($cList as $cData) {
	$categoriesList[$cData['id']] = $cData;
}
if(!is_array($categoriesList)) throw new Exception('There are no categories!');

if(check($_GET['category'])) {
	if(array_key_exists($_GET['category'], $categoriesList)) {
		$category = $_GET['category'];
		
		echo '<h1>'.$categoriesList[$category]['title'].'</h1>';
	}
}

if(check($category)) {
	$itemList = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_items` WHERE `status` = 1 AND `category` = ? ORDER BY `id` DESC", array($category));
	
	echo '<a href="'.__BASE_URL__.'webshop/add/category/'.$category.'" class="btn btn-lg btn-success">Add Item</a>';
	echo '<br /><br />';
	
} else {
	$itemList = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_items` WHERE `status` = 1 ORDER BY `id` DESC");
}
if(!is_array($itemList)) throw new Exception("There are no items to display.");

echo '<table class="table">';
	
	echo '<thead>';
		echo '<tr>';
			echo '<th>Item Id</th>';
			echo '<th>Item Name</th>';
			echo '<th>Enchantment</th>';
			echo '<th>Temperance</th>';
			echo '<th>Category</th>';
			echo '<th>Cost</th>';
			echo '<th style="text-right">Actions</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($itemList as $item) {
		
		if(!check($item['name'])) {
			$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($item['item_id']));
			$itemName = (is_array($itemInfo) ? $itemInfo['item_name'] : 'unknown');
		} else {
			$itemName = $item['name'];
		}
		$itemCategory = array_key_exists($item['category'], $categoriesList) ? $categoriesList[$item['category']]['title'] : 'unknown';
		
		echo '<tr>';
			echo '<td>'.$item['item_id'].'</td>';
			echo '<td>'.$item['count'].'x '.$itemName.'</td>';
			echo '<td>'.$item['enchantment'].'</td>';
			echo '<td>'.$item['temperance'].'</td>';
			echo '<td>'.$itemCategory.' ('.$item['category'].')</td>';
			echo '<td>'.$item['cost'].'</td>';
			echo '<td>';
				echo '<a href="'.__BASE_URL__.'webshop/edit/id/'.$item['id'].'" class="btn btn-warning btn-xs">Edit</a> ';
				echo '<a href="'.__BASE_URL__.'webshop/items/category/'.$category.'/delete/'.$item['id'].'" class="btn btn-danger btn-xs">Delete</a>';
			echo '</td>';
		echo '</tr>';
	}
	echo '</tbody>';
echo '</table>';