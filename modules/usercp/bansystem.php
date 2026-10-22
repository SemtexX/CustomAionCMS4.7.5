<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block usercp"></div>
<br /><br />

<h3>Ban System</h3>

<br /><br />

<?php

try {
	# load database
	$db = Handler::loadDB();

	# get account bans
	$permanentBans = $db->queryFetch("SELECT * FROM `aioncms`.`permanent_bans` WHERE `account` = ? ORDER BY `id` DESC", array($_SESSION['username']));
	$temporalBans = $db->queryFetch("SELECT * FROM `aioncms`.`temporal_bans` WHERE `account` = ? ORDER BY `id` DESC", array($_SESSION['username']));
	
	
	echo '<h4>Permanent Bans:</h4>';
	if(is_array($permanentBans)) {
		
		echo '<table class="table table-bordered table-hover">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Id</th>';
					echo '<th>Reason</th>';
					echo '<th>Date</th>';
					echo '<th>Status</th>';
					echo '<th></th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($permanentBans as $ban) {
				echo '<tr>';
					echo '<td>#'.$ban['id'].'</td>';
					echo '<td>'.$ban['reason'].'</td>';
					echo '<td>'.date("Y-m-d", strtotime($ban['date'])).'</td>';
					echo '<td>'.($ban['staff_lock'] == 0 ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Locked</span>').'</td>';
					echo '<td><a href="'.module_url('bansystem/view/type/permanent/id/'.$ban['id'].'/', true).'" class="btn btn-xs btn-primary">view</a></td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	} else {
		message('<strong>Good Job!</strong> You don\'t have any permanent bans.', 'warning');
	}
	
	echo '<br /><br />';
	
	echo '<h4>Temporal Bans:</h4>';
	if(is_array($temporalBans)) {
		
		echo '<table class="table table-bordered table-hover">';
			echo '<thead>';
				echo '<tr>';
					echo '<th>Id</th>';
					echo '<th>Reason</th>';
					echo '<th>Date</th>';
					echo '<th>Duration</th>';
					echo '<th>Status</th>';
					echo '<th></th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($temporalBans as $ban) {
				
				$banDuration = sec_to_dhms(strtotime($ban['end_date'])-strtotime($ban['start_date']));

				echo '<tr>';
					echo '<td>#'.$ban['id'].'</td>';
					echo '<td>'.$ban['reason'].'</td>';
					echo '<td>'.date("Y-m-d", strtotime($ban['start_date'])).'</td>';
					echo '<td>'.$banDuration[0].'d '.$banDuration[1].'hrs</td>';
					echo '<td>'.($ban['staff_lock'] == 0 ? '<span class="label label-success">Open</span>' : '<span class="label label-danger">Locked</span>').'</td>';
					echo '<td><a href="'.module_url('bansystem/view/type/temporal/id/'.$ban['id'].'/', true).'" class="btn btn-xs btn-primary">view</a></td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	} else {
		message('<strong>Good Job!</strong> You don\'t have any temporal bans.', 'warning');
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}

?>