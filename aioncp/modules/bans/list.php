<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

echo '<h4>Permanent Bans:</h4>';
$permanentBans = $gabs->queryFetch("SELECT * FROM `permanent_bans` WHERE `staff` = ? ORDER BY `id` DESC", array($_SESSION['aioncp']['name']));
if(is_array($permanentBans)) {
	echo '<table class="table table-condensed table-hover">';
		echo '<tr>';
			echo '<th>Id</th>';
			echo '<th>Account</th>';
			echo '<th>Reason</th>';
			echo '<th>Date</th>';
			echo '<th>Status</th>';
			echo '<th>Last Msg.</th>';
			echo '<th></th>';
		echo '</tr>';
		foreach($permanentBans as $row) {
			echo '<tr>';
				echo '<td>'.$row['id'].'</td>';
				echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['account'].'">'.$row['account'].'</a></td>';
				echo '<td>'.$row['reason'].'</td>';
				echo '<td>'.$row['date'].'</td>';
				echo '<td>'.($row['staff_lock'] == 1 ? '<span class="label label-danger">locked</span>' : '<span class="label label-success">open</span>').'</td>';
				echo '<td>'.$row['last_message_by'].'</td>';
				echo '<td><a href="'.__BASE_URL__.'bans/view/type/permanent/id/'.$row['id'].'" class="btn btn-primary btn-xs">open</a></td>';
			echo '</tr>';
		}
	echo '</table>';
} else {
	message('You have not permanently banned any account / character.', 'warning');
}

echo '<hr>';

echo '<h4>Temporal Bans:</h4>';
$temporalBans = $gabs->queryFetch("SELECT * FROM `temporal_bans` WHERE `staff` = ? ORDER BY `id` DESC", array($_SESSION['aioncp']['name']));
if(is_array($temporalBans)) {
	echo '<table class="table table-condensed table-hover">';
		echo '<tr>';
			echo '<th>Id</th>';
			echo '<th>Account</th>';
			echo '<th>Reason</th>';
			echo '<th>Start Date</th>';
			echo '<th>End Date</th>';
			echo '<th>Status</th>';
			echo '<th>Last Msg.</th>';
			echo '<th></th>';
		echo '</tr>';
		foreach($temporalBans as $row) {
			echo '<tr>';
				echo '<td>'.$row['id'].'</td>';
				echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['account'].'">'.$row['account'].'</a></td>';
				echo '<td>'.$row['reason'].'</td>';
				echo '<td>'.$row['start_date'].'</td>';
				echo '<td>'.$row['end_date'].'</td>';
				echo '<td>'.($row['staff_lock'] == 1 ? '<span class="label label-danger">locked</span>' : '<span class="label label-success">open</span>').'</td>';
				echo '<td>'.$row['last_message_by'].'</td>';
				echo '<td><a href="'.__BASE_URL__.'bans/view/type/temporal/id/'.$row['id'].'" class="btn btn-primary btn-xs">open</a></td>';
			echo '</tr>';
		}
	echo '</table>';
} else {
	message('You have not permanently banned any account / character.', 'warning');
}