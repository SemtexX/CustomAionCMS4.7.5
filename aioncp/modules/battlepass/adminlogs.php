<?php
/**
 * AionCMS
 * https://aioncms.com
 *
 * BattlePass — Admin: points log and claims
 */

$seasonFilter = null;
if(check($_GET['season'])) {
	if(Validator::UnsignedNumber($_GET['season'])) {
		$seasonFilter = (int)$_GET['season'];
	}
}

echo '<div class="row">';

	# POINTS LOG
	echo '<div class="col-md-12">';
		echo '<div class="block">';
			echo '<div class="block-content">';

				echo '<h3>Points Log</h3>';

				if($seasonFilter !== null) {
					$logs = $db->queryFetch(
						"SELECT * FROM `aioncms`.`battlepass_points_log`
						 WHERE `season_id` = ?
						 ORDER BY `id` DESC LIMIT 200",
						array($seasonFilter)
					);
				} else {
					$logs = $db->queryFetch(
						"SELECT * FROM `aioncms`.`battlepass_points_log`
						 ORDER BY `id` DESC LIMIT 200"
					);
				}

				if(is_array($logs)) {
					echo '<table class="table table-striped table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>#</th>';
							echo '<th>Account</th>';
							echo '<th>Season</th>';
							echo '<th>Source</th>';
							echo '<th>Points</th>';
							echo '<th>Reference</th>';
							echo '<th>Time</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($logs as $log) {
						
						// Source-Name formatieren
						$sourceName = $log['source'];
						switch($log['source']) {
							case 'daily_login': $sourceName = 'Daily Login'; break;
							case 'shop_purchase': $sourceName = 'Shop Purchase'; break;
							case 'pve_kill': $sourceName = 'PvE Kill'; break;
							case 'quest_complete': $sourceName = 'Quest Complete'; break;
							case 'admin': $sourceName = 'Admin Grant'; break;
							case 'other': $sourceName = 'Other'; break;
						}
						
						echo '<tr>';
							echo '<td>'.$log['id'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'tools/accountdata/id/'.$log['account_id'].'" target="_blank">'.$log['account_id'].'</a></td>';
							echo '<td>'.$log['season_id'].'</td>';
							echo '<td>'.$sourceName.'</td>';
							echo '<td>'.($log['points'] > 0 ? '+' : '').$log['points'].'</td>';
							echo '<td>'.htmlspecialchars($log['reference']).'</td>';
							echo '<td>'.date("d.m.Y H:i:s", strtotime($log['created_at'])).'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					message('There are no points log entries to display.', 'warning');
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

	# CLAIMS
	echo '<div class="col-md-12">';
		echo '<div class="block">';
			echo '<div class="block-content">';

				echo '<h3>Claims</h3>';

				if($seasonFilter !== null) {
					$claims = $db->queryFetch(
						"SELECT c.*, t.`tier_number`
						 FROM `aioncms`.`battlepass_claims` c
						 LEFT JOIN `aioncms`.`battlepass_tiers` t ON t.`id` = c.`tier_id`
						 WHERE c.`season_id` = ?
						 ORDER BY c.`id` DESC LIMIT 200",
						array($seasonFilter)
					);
				} else {
					$claims = $db->queryFetch(
						"SELECT c.*, t.`tier_number`
						 FROM `aioncms`.`battlepass_claims` c
						 LEFT JOIN `aioncms`.`battlepass_tiers` t ON t.`id` = c.`tier_id`
						 ORDER BY c.`id` DESC LIMIT 200"
					);
				}

				if(is_array($claims)) {
					echo '<table class="table table-striped table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>#</th>';
							echo '<th>Account</th>';
							echo '<th>Season</th>';
							echo '<th>Tier</th>';
							echo '<th>Character</th>';
							echo '<th>Claimed At</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($claims as $claim) {
						echo '<tr>';
							echo '<td>'.$claim['id'].'</td>';
							echo '<td><a href="'.__BASE_URL__.'tools/accountdata/id/'.$claim['account_id'].'" target="_blank">'.$claim['account_id'].'</a></td>';
							echo '<td>'.$claim['season_id'].'</td>';
							echo '<td>'.$claim['tier_number'].'</td>';
							echo '<td>'.htmlspecialchars($claim['character_name']).'</td>';
							echo '<td>'.date("d.m.Y H:i:s", strtotime($claim['claimed_at'])).'</td>';
						echo '</tr>';
					}
					echo '</tbody>';
					echo '</table>';
				} else {
					message('There are no claims to display.', 'warning');
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';