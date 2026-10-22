<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	$stashStatus = (check($_GET['show']) ? $_GET['show'] : 0);
	$result = $db->queryFetch("SELECT `lottery_stash`.`userid`, `lottery_stash`.`credits`, `lottery_stash`.`note`, `account_data`.`name` FROM `lottery_stash` INNER JOIN `account_data` ON `account_data`.`id` = `lottery_stash`.`userid` WHERE `locked` = ? ORDER BY `lottery_stash`.`credits` DESC", array($stashStatus));
	if(is_array($result)) {
		echo '<p><a href="?show=0">unlocked</a> | <a href="?show=1">locked</a> | <a href="?show=1&note=1">locked (n)</a></p>';
		echo '<table style="min-width: 500px;">';
			foreach($result as $stash) {
				echo '<tr>';
					echo '<td style="padding: 0px 20px;">'.$stash['userid'].'</td>';
					echo '<td style="padding: 0px 20px;">'.$stash['name'].'</td>';
					echo '<td style="padding: 0px 20px;">'.$stash['credits'].'</td>';
					if(check($_GET['note'])) echo '<td style="padding: 0px 20px;">'.$stash['note'].'</td>';
				echo '</tr>';
			}
		echo '</table>';
	}
	
	
} catch (Exception $ex) {
	message($ex->getMessage(), 'error');
}