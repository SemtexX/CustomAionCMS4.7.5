<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


if(check($_POST['submit'])) {
	try {
		
		if(!check($_POST['id'])) throw new Exception('Invalid id (not set).');
		if(!Validator::UnsignedNumber($_POST['id'])) throw new Exception('Invalid id (not a number).');
		
		if(!check($_POST['title'])) throw new Exception('Invalid title (not set).');
		//if(!Validator::AlphaNumeric($_POST['title'])) throw new Exception('Invalid title (only alphanumeric).');
		if(!Validator::Length($_POST['title'], 25, 3)) throw new Exception('Invalid title length (3 - 25).');
		
		if(!check($_POST['order'])) throw new Exception('Invalid order (not set).');
		if(!Validator::UnsignedNumber($_POST['order'])) throw new Exception('Invalid order (not a number).');
		if(!Validator::Number($_POST['order'], 999, 1)) throw new Exception('Invalid order value (1 - 999).');
		
		$categoryInfo = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ?", array($_POST['id']));
		if(!is_array($categoryInfo)) throw new Exception('The category id is not valid.');
		
		if(check($_POST['parent'])) {
			if(!Validator::UnsignedNumber($_POST['parent'])) throw new Exception('Invalid parent (not a number).');
			
			$checkParent = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ?", array($_POST['parent']));
			if(!is_array($checkParent)) throw new Exception('The parent category id is not valid.');
			
			$updateInfo = $db->query("UPDATE `aioncms`.`website_shop_categories` SET `title` = ?, `order` = ?, `parent` = ? WHERE `id` = ?", array(strtolower($_POST['title']), $_POST['order'], $_POST['parent'], $_POST['id']));
		} else {
			$updateInfo = $db->query("UPDATE `aioncms`.`website_shop_categories` SET `title` = ?, `order` = ? WHERE `id` = ?", array(strtolower($_POST['title']), $_POST['order'], $_POST['id']));
		}
		
		if(!$updateInfo) throw new Exception('Error editing (something went terribly wrong).');
		
		message('Edit successful.', 'success');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

if(check($_GET['id'])) {
	try {
		
		if(!check($_GET['id'])) throw new Exception('Invalid id (not set).');
		if(!Validator::UnsignedNumber($_GET['id'])) throw new Exception('Invalid id (not a number).');
		
		if(!check($_GET['status'])) throw new Exception('Invalid request.');
		if(!in_array($_GET['status'], array('enable', 'disable'))) throw new Exception('Invalid request.');
		
		$categoryInfo = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ?", array($_GET['id']));
		if(!is_array($categoryInfo)) throw new Exception('The category id is not valid.');
		
		if($_GET['status'] == 'enable') {
			$updateInfo = $db->query("UPDATE `aioncms`.`website_shop_categories` SET `status` = ? WHERE `id` = ?", array(1, $_GET['id']));
		} else {
			$updateInfo = $db->query("UPDATE `aioncms`.`website_shop_categories` SET `status` = ? WHERE `id` = ?", array(0, $_GET['id']));
		}
		
		if(!$updateInfo) throw new Exception('Error editing (something went terribly wrong).');
		
		//message('Edit successful.', 'success');
		redirect('webshop/categories/');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

if(check($_POST['add_category'])) {
	try {
		
		if(!check($_POST['add_parent'])) throw new Exception('Incomplete request.');
		if(!Validator::UnsignedNumber($_POST['add_parent'])) throw new Exception('Incomplete request.');
		
		if(!check($_POST['add_title'])) throw new Exception('Invalid title (not set).');
		//if(!Validator::AlphaNumeric($_POST['add_title'])) throw new Exception('Invalid title (only alphanumeric).');
		if(!Validator::Length($_POST['add_title'], 25, 3)) throw new Exception('Invalid title length (3 - 25).');
		
		if($_POST['add_parent'] == 0) {
			
			# will be category
			$addCategory = $db->query("INSERT INTO `aioncms`.`website_shop_categories` (`title`, `status`) VALUES (?, ?)", array(strtolower($_POST['add_title']), 0));
			if(!$addCategory) throw new Exception('Could not add.');
			
		} else {
			
			# will be sub-category
			$parentInfo = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `id` = ?", array($_POST['add_parent']));
			if(!is_array($parentInfo)) throw new Exception('The parent category is not valid.');
			
			$addCategory = $db->query("INSERT INTO `aioncms`.`website_shop_categories` (`title`, `parent`, `status`) VALUES (?, ?, ?)", array(strtolower($_POST['add_title']), $_POST['add_parent'], 0));
			if(!$addCategory) throw new Exception('Could not add.');
			
		}
		
		message('Category added!', 'success');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';
	echo '<div class="col-md-8">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				
				// CATEGORY LIST
				$result = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `parent` IS NULL ORDER BY `order` ASC, `id` ASC");
				
				echo '<table class="table table-hover">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Title</th>';
						echo '<th style="width:30px;">ID</th>';
						echo '<th style="width:80px;">Parent</th>';
						//echo '<th style="width:80px;">Order</th>';
						echo '<th style="width:80px;">Status</th>';
						echo '<th style="width:150px;"></th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
					foreach($result as $row) {
						
						echo '<form action="'.__BASE_URL__.'webshop/categories/" method="post">';
						echo '<input type="hidden" name="id" value="'.$row['id'].'"/>';
						echo '<tr>';
							echo '<td><input class="form-control" type="text" name="title" value="'.ucfirst($row['title']).'"/></td>';
							echo '<td>'.$row['id'].'</td>';
							echo '<td></td>';
							//echo '<td><input class="form-control" type="text" name="order" value="'.$row['order'].'"/></td>';
							if($row['status'] == 1) {
								echo '<td><a href="'.__BASE_URL__.'webshop/categories/id/'.$row['id'].'/status/disable/" class="btn btn-success btn-xs push-5-r push-10"><i class="fa fa-check"></i></a></td>';
							} else {
								echo '<td><a href="'.__BASE_URL__.'webshop/categories/id/'.$row['id'].'/status/enable/" class="btn btn-warning btn-xs push-5-r push-10"><i class="fa fa-exclamation-circle"></i></a></td>';
							}
							echo '<td><button class="btn btn-default btn-xs" type="submit" name="submit" value="ok"/>Save</button></td>';
						echo '</tr>';
						echo '</form>';
						
						# childs
						$childs = $db->queryFetch("SELECT * FROM `aioncms`.`website_shop_categories` WHERE `parent` = ? ORDER BY `order` ASC, `id` ASC", array($row['id']));
						if(is_array($childs)) {
							foreach($childs as $child) {
								echo '<form action="'.__BASE_URL__.'webshop/categories/" method="post">';
								echo '<input type="hidden" name="id" value="'.$child['id'].'"/>';
								echo '<tr>';
									//echo '<td style="padding-left: 50px;">'.ucfirst($child['title']).'</td>';
									echo '<td style="padding-left: 50px;"><input class="form-control" type="text" name="title" value="'.ucfirst($child['title']).'"/></td>';
									echo '<td>'.$child['id'].'</td>';
									echo '<td><input class="form-control" type="text" name="parent" value="'.$child['parent'].'"/></td>';
									//echo '<td><input class="form-control" type="text" name="order" value="'.$child['order'].'"/></td>';
									if($child['status'] == 1) {
										echo '<td><a href="'.__BASE_URL__.'webshop/categories/id/'.$child['id'].'/status/disable/" class="btn btn-success btn-xs push-5-r push-10"><i class="fa fa-check"></i></a></td>';
									} else {
										echo '<td><a href="'.__BASE_URL__.'webshop/categories/id/'.$child['id'].'/status/enable/" class="btn btn-warning btn-xs push-5-r push-10"><i class="fa fa-exclamation-circle"></i></a></td>';
									}
									echo '<td><button class="btn btn-default btn-xs" type="submit" name="submit" value="ok"/>Save</button> <a href="'.__BASE_URL__.'webshop/items/category/'.$child['id'].'" class="btn btn-info btn-xs">Items</a> <a href="'.__BASE_URL__.'webshop/add/category/'.$child['id'].'" class="btn btn-success btn-xs">+</a></td>';
								echo '</tr>';
								echo '</form>';
							}
						}
						
						echo '<tr><td colspan="3"><br /></td></tr>'; // separator
					}
				echo '</tbody>';
				echo '</table>';

			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	# RIGHT
	echo '<div class="col-md-4">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				
				echo '<form action="'.__BASE_URL__.'webshop/categories/" method="post">';
						echo '<div class="form-group">';
							echo '<select class="form-control" name="add_parent">';
								echo '<option value="0">No Parent</option>';
								foreach($result as $cats) {
									echo '<option value="'.$cats['id'].'">'.ucfirst($cats['title']).'</option>';
								}
							echo '</select>';
						echo '</div>';
						echo '<div class="form-group">';
							echo '<input type="text" name="add_title" class="form-control" placeholder="Title...">';
						echo '</div>';
						echo '<button type="submit" class="btn btn-success" name="add_category" value="ok">Add Category</button>';
					echo '</form><br />';
					
					echo '<p>By default new categories and sub-categories will be added with their status disabled.</p>';
					
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';