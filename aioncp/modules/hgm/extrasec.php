<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

try {
	
	$result = $db->queryFetch("SELECT
`account_data`.`id`, `account_data`.`name`, `account_data`.`email`, `account_data`.`last_ip`, `account_data`.`last_mac`,
`account_data`.`credits`, `account_security`.`email_confirmed`
FROM `account_security`
INNER JOIN `account_data` ON `account_data`.`id` = `account_security`.`id`", array());
	if(is_array($result)) {
		foreach($result as $key => $row) {
			
			echo '<table style="width:100%;table-layout: fixed;">';
			
			if($key == 0) {
				echo '<tr>';
					foreach($row as $k => $r) {
						echo '<td style="background:#000;color:#ffcc00;font-size:12px;">'.$k.'</td>';
					}
				echo '</tr>';
			}
			
			if($row['email_confirmed'] == 1) {
				echo '<tr style="background:#ABFFAE;">';
			} else {
				echo '<tr>';
			}
			
			foreach($row as $kk => $rr) {
				echo '<td style="font-size:12px;">'.$rr.'</td>';
			}
			echo '</tr>';
			
			echo '</table>';
		}
	}
	
	
} catch (Exception $ex) {
	die($ex->getMessage());
}