<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	$Shop = new Shop();

	if(isLoggedIn()) {
		$Shop->setUserId($_SESSION['userid']);
		$accountData = $Shop->getAccountData();
	}
	
	echo '<div class="shop-container">';
		echo '<div class="shop-header">';
			echo '<div class="row text-center">';
				echo '<a href="'.module_url('shop/', true).'"><img src="'.template_img(true).'webshop/shop_header_title.png" /></a>';
			echo '</div>';
			
			if(isLoggedIn()) {
				echo '<div class="row">';
					echo '<div class="col-md-3 text-center">';
						echo 'ACCOUNT<br />';
						echo '<a href="'.module_url('usercp/', true).'">usercp</a>';
					echo '</div>';
					echo '<div class="col-md-3 text-center">';
						echo 'CREDITS<br />';
						echo '<a href="'.module_url('donate/', true).'">'.number_format($accountData['toll']).' (add)</a>';
					echo '</div>';
					echo '<div class="col-md-3 text-center">';
						echo 'HISTORY<br />';
						echo '<a href="'.module_url('shop/history/', true).'">list</a>';
					echo '</div>';
					echo '<div class="col-md-3 text-center">';
						echo 'HELP<br />';
						echo '<a href="'.config('forum_url', true).'" target="_blank">forum</a>';
					echo '</div>';
				echo '</div>';
			}
			
		echo '</div>';
			
		echo '<div class="shop-content">';
			echo '<div class="row">';
				echo '<div class="col-md-3 sidebar">';
					$Shop->displayMenu();
				echo '</div>';
				echo '<div class="col-md-9 items-content">';
					
					echo '<div class="row text-center">';
						echo '<span style="font-weight:bold;font-size:24px;">Weekly Specials</span><br /><br />';
					echo '</div>';
					$Shop->listWeeklySpecialItems();
					
					/*
					echo '<div>';
						echo 'Welcome to the Shop, please choose a category.';
					echo '</div>';
					*/
					
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
} catch(Exception $ex) {
	# ERROR
	echo '<div class="shop-container">';
		echo '<div class="shop-header">';
			echo '<div class="row text-center">';
				echo '<a href="'.module_url('shop/', true).'"><img src="'.template_img(true).'webshop/shop_header_title.png" /></a>';
			echo '</div>';
		echo '</div>';
			
		echo '<div class="shop-content">';
			echo '<div class="row">';
				echo '<div class="col-md-12 items-content">';
					message($ex->getMessage(), 'error');
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}