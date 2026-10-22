<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

if($_GET['key'] != 789789789) die();

# Define Access
define('access', 'index');

# Load WebEngine
if(!@include_once('../includes/system.php')) die('[ERROR] Could not load system.');


$result = $aioncp->queryFetch("SELECT * FROM `web_logs` ORDER BY `id` DESC LIMIT 50");
if(is_array($result)) {
	
	echo '<table class="table table-condensed table-hover">';
	echo '<tr>';
		//echo '<th>#</th>';
		echo '<th>Account</th>';
		echo '<th>Ip</th>';
		echo '<th>Location</th>';
		echo '<th>Time</th>';
		echo '<th>Message</th>';
	echo '</tr>';
	foreach($result as $row) {
		echo '<tr>';
			//echo '<td>'.$row['id'].'</td>';
			echo '<td>'.$row['account'].'</td>';
			echo '<td>'.$row['ip_address'].'</td>';
			echo '<td>'.$row['location'].'</td>';
			echo '<td>'.$row['timestamp'].'</td>';
			echo '<td>'.$row['message'].'</td>';
		echo '</tr>';
	}
	echo '</table>';
	
}