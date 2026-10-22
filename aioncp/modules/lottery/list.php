<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

$list = $db->queryFetch("SELECT * FROM `lottery` ORDER BY `id` DESC", array());

if(is_array($list)) {
	
	echo '<table class="table table-hover">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Id</th>';
			echo '<th>Initial Jackpot</th>';
			echo '<th>Start Date</th>';
			echo '<th>End Date</th>';
			echo '<th>Numbers</th>';
			echo '<th></th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($list as $row) {
		echo ($row['finished'] == 1 ? '<tr>' : '<tr class="success">');
			echo '<td><span style="font-weight:bold;color:red;">'.$row['name'].'</span></td>';
			echo '<td>'.$row['current_jackpot'].'</td>';
			echo '<td>'.date("Y-m-d h:i A", $row['start_timestamp']).'</td>';
			echo '<td>'.date("Y-m-d h:i A", $row['end_timestamp']).'</td>';
			echo '<td>'.($row['finished'] == 1 ? $row['number1'].', '.$row['number2'].', '.$row['number3'].', '.$row['number4'] : '').'</td>';
			echo '<td><a href="'.__BASE_URL__.'lottery/status/id/'.$row['name'].'" class="btn btn-xs btn-primary">More Info</a></td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
}