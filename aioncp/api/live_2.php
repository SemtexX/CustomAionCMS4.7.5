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


$result = $aioncp->queryFetch("SELECT * FROM `website_session_control` ORDER BY `id` DESC");
if(is_array($result)) {
	echo '<table class="table table-condensed table-hover">';
	echo '<tr>';
		//echo '<th>#</th>';
		echo '<th>Account</th>';
		echo '<th>Session ID</th>';
		echo '<th>Last Location</th>';
		echo '<th>Last Activity</th>';
		echo '<th>Session IP</th>';
	echo '</tr>';
	foreach($result as $row) {
		
		if($row['userid'] == '-1') {
			$user = 'guest';
		} else {
			$accountdata = $db->queryFetchSingle("SELECT * FROM `account_data` WHERE `id` = ?", array($row['userid']));
			if(!is_array($accountdata)) continue;
			
			$user = $accountdata['name'];
		}
		
		
		echo '<tr>';
			//echo '<td>'.$row['id'].'</td>';
			echo '<td>'.$user.'</td>';
			echo '<td>'.$row['session_id'].'</td>';
			echo '<td>'.$row['last_location'].'</td>';
			echo '<td>'.$row['active'].'</td>';
			echo '<td>'.$row['session_ip'].'</td>';
		echo '</tr>';
	}
	echo '</table>';
	
}