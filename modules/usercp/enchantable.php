<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block itemenchant"></div>
<br /><br />

<h3>Enchantable Items List</h3>
<p>The following items are available to be enchanted using the web tool.</p>
<br />

<?php
try {
	
	$enchantableItems = config('enchantable_items', true);
	
	echo '<table class="table">';
	foreach($enchantableItems as $itemId => $skills) {
		
		$itemName = getItemName($itemId);
		if(!check($itemName)) continue;
		
		echo '<tr>';
			echo '<td>'.$itemName.'</td>';
		echo '</tr>';
	}
	echo '</table>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}