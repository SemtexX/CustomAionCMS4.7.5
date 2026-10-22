<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

/*$versionCheck = checkVersion();
if(check($versionCheck)) {
	if(is_array($versionCheck)) {
		if($versionCheck['update']) {
			message('<strong>AionCMS v'.$versionCheck['version'].'</strong> is available! More information at: <a href="https://aioncms.com/" target="_blank">www.aioncms.com</a>. (You are currently on v'.__AIONCMS_VERSION__.')', 'warning');
		}
	}
}*/

echo '<div class="row">';
	
	# -----------
	echo '<div class="col-sm-12 col-md-12 col-lg-4">';
		echo '<div class="block block-rounded block-link-hover3">';
			echo '<div class="block-content block-content-full clearfix">';
				echo '<div class="col-md-9">';
					echo '<div class="font-w600 push-5" style="color:#a48ad4;">Account Info</div>';
					echo '<div>';
						echo '<form class="form-horizontal" action="'.__BASE_URL__.'tools/accountdata/" method="post">';
							echo '<div class="form-material">';
								echo '<input class="form-control" type="text" id="material-text" name="account_name">';
								echo '<input type="hidden" name="account_submit" value="ok"/>';
							echo '</div>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
				echo '<div class="col-md-3 text-right">';
					echo '<i class="si si-frame fa-4x" style="color:#a48ad4;"></i>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	# -----------
	
	# -----------
	echo '<div class="col-sm-12 col-md-12 col-lg-4">';
		echo '<div class="block block-rounded block-link-hover3">';
			echo '<div class="block-content block-content-full clearfix">';
				echo '<div class="col-md-9">';
					echo '<div class="font-w600 push-5" style="color:#ff6b6b;">Player Info</div>';
					echo '<div>';
						echo '<form class="form-horizontal" action="'.__BASE_URL__.'tools/playerdetails/" method="post">';
							echo '<div class="form-material">';
								echo '<input class="form-control" type="text" id="material-text" name="player_name">';
								echo '<input type="hidden" name="player_submit" value="ok"/>';
							echo '</div>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
				echo '<div class="col-md-3 text-right">';
					echo '<i class="si si-user fa-4x" style="color:#ff6b6b;"></i>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	# -----------
	
	# -----------
	echo '<div class="col-sm-12 col-md-12 col-lg-4">';
		echo '<div class="block block-rounded block-link-hover3">';
			echo '<div class="block-content block-content-full clearfix">';
				echo '<div class="col-md-9">';
					echo '<div class="font-w600 push-5" style="color:#14adc4;">Legion Info</div>';
					echo '<div>';
						echo '<form class="form-horizontal" action="'.__BASE_URL__.'tools/legiondetails/" method="post">';
							echo '<div class="form-material">';
								echo '<input class="form-control" type="text" id="material-text" name="legion_name">';
								echo '<input type="hidden" name="legion_submit" value="ok"/>';
							echo '</div>';
						echo '</form>';
					echo '</div>';
				echo '</div>';
				echo '<div class="col-md-3 text-right">';
					echo '<i class="si si-flag fa-4x" style="color:#14adc4;"></i>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	# -----------
	
echo '</div>';

echo '<div class="row">';
	echo '<div class="col-md-8">';
	
		// REQUIRING ACTION BANS
		echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">Bans Requiring Action</div>';
			echo '<div class="panel-body">';
				
				$permanentBans = $gabs->queryFetch("SELECT * FROM `permanent_bans` WHERE `requires_action` = 1 ORDER BY `date` ASC", array());
				if(is_array($permanentBans)) {
					echo '<h4>Permanent:</h4>';
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
				}
				
				$temporalBans = $gabs->queryFetch("SELECT * FROM `temporal_bans` WHERE `requires_action` = 1 ORDER BY `start_date` ASC", array());
				if(is_array($temporalBans)) {
					echo '<h4>Temporal:</h4>';
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
					foreach($temporalBans as $row) {
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
				}
				
			echo '</div>';
		echo '</div>';
		// <-- END REQUIRING ACTION BANS
		
	echo '</div>';
	
	echo '<div class="col-md-4">';

		echo '<div class="panel panel-primary">';
			echo '<div class="panel-heading">Weekly Special Shop</div>';
			echo '<div class="panel-body">';
			$specialShopItems = $db->queryFetch("SELECT * FROM `aioncms`.`weeklyspecial_activeitems`", array());
			if(is_array($specialShopItems)) {
				
				echo '<table class="table">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>Item</th>';
						echo '<th>Qty.</th>';
						echo '<th>Cost</th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				foreach($specialShopItems as $item) {
					$itemInfo = $db->queryFetchSingle("SELECT * FROM `aion_itemlist` WHERE `item_id` = ?", array($item['item_id']));
					if(!is_array($itemInfo)) continue;
					
					if($item['quantity'] == 0) {
						echo '<tr class="warning">';
					} else {
						echo '<tr>';
					}
						echo '<td>'.$itemInfo['item_name'].'</td>';
						echo '<td>'.$item['quantity'].'/5</td>';
						echo '<td>'.$item['cost'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
				
			} else {
				message('No items.', 'warning');
			}
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
echo '</div>';
