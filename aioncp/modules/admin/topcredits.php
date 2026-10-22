<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */


try {
	
	$topCredits = $db->queryFetch("SELECT `name`, `toll` FROM `account_data` ORDER BY `toll` DESC LIMIT 100", array());
	if(!is_array($topCredits)) throw new Exception('There are no results.');
	
	echo '<div class="col-md-6">';
		echo '<div class="block">';
			echo '<div class="block-content">';
				echo '<table class="table table-hover">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Account</th>';
						echo '<th>Credits</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($topCredits as $account) {
					echo '<tr>';
						echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.urlencode($account['name']).'">'.$account['name'].'</a></td>';
						echo '<td>'.$account['toll'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}