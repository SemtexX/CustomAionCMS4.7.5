<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

echo '<div class="row">';
	echo '<div class="col-md-12">';
		echo '<div class="panel panel-danger">';
			echo '<div class="panel-heading">Permanent Bans</div>';
			echo '<div class="panel-body">';
				
				$permanentBans = $gabs->queryFetch("SELECT * FROM `permanent_bans` ORDER BY `id` DESC LIMIT 25", array());
				if(is_array($permanentBans)) {
					echo '<table class="table table-condensed table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Id</th>';
							echo '<th>Account</th>';
							echo '<th>By</th>';
							echo '<th>Reason</th>';
							echo '<th>Date</th>';
							echo '<th>Status</th>';
							echo '<th>Last Msg.</th>';
							echo '<th></th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($permanentBans as $row) {
						echo '<tr>';
							echo '<td>'.$row['id'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['account'].'">'.$row['account'].'</a></td>';
							echo '<td>'.$row['staff'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['date'].'</td>';
							echo '<td>'.($row['staff_lock'] == 1 ? '<span class="label label-danger">Locked</span>' : '<span class="label label-success">Open</span>').'</td>';
							echo '<td>'.$row['last_message_by'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'bans/view/type/permanent/id/'.$row['id'].'" class="btn btn-primary btn-xs">open</a></td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					message('No data.', 'warning');
				}
				
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="row">';
	echo '<div class="col-md-12">';
		echo '<div class="panel panel-warning">';
			echo '<div class="panel-heading">Temporal Bans</div>';
			echo '<div class="panel-body">';
				
				$permanentBans = $gabs->queryFetch("SELECT * FROM `temporal_bans` ORDER BY `id` DESC LIMIT 25", array());
				if(is_array($permanentBans)) {
					echo '<table class="table table-condensed table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>Id</th>';
							echo '<th>Account</th>';
							echo '<th>By</th>';
							echo '<th>Reason</th>';
							echo '<th>S. Date</th>';
							echo '<th>E. Date</th>';
							echo '<th>Status</th>';
							echo '<th>Last Msg.</th>';
							echo '<th></th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($permanentBans as $row) {
						echo '<tr>';
							echo '<td>'.$row['id'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'tools/accountdata/name/'.$row['account'].'">'.$row['account'].'</a></td>';
							echo '<td>'.$row['staff'].'</td>';
							echo '<td>'.$row['reason'].'</td>';
							echo '<td>'.$row['start_date'].'</td>';
							echo '<td>'.$row['end_date'].'</td>';
							echo '<td>'.($row['staff_lock'] == 1 ? '<span class="label label-danger">Locked</span>' : '<span class="label label-success">Open</span>').'</td>';
							echo '<td>'.$row['last_message_by'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'bans/view/type/temporal/id/'.$row['id'].'" class="btn btn-primary btn-xs">open</a></td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					message('No data.', 'warning');
				}
				
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';