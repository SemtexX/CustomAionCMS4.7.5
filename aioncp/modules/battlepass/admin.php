<?php
/**
 * AionCMS
 * https://aioncms.com
 *
 * BattlePass — Admin: Season management
 */

# ---- ADD SEASON ----
if(check($_POST['add_season'])) {
	try {

		if(!check($_POST['season_name'])) throw new Exception('You must provide a season name.');
		if(!Validator::Length($_POST['season_name'], 100, 3)) throw new Exception('Season name must be between 3 and 100 characters.');

		if(!check($_POST['season_start'])) throw new Exception('You must provide a start date.');
		if(!check($_POST['season_end'])) throw new Exception('You must provide an end date.');
		if(strtotime($_POST['season_end']) <= strtotime($_POST['season_start'])) throw new Exception('The end date must be after the start date.');

		# Fix für datetime-local Format
		$start_date = str_replace('T', ' ', $_POST['season_start']) . ':00';
		$end_date   = str_replace('T', ' ', $_POST['season_end']) . ':00';

		if(!check($_POST['points_per_credit'])) throw new Exception('You must provide points per credit.');
		if(!Validator::UnsignedNumber($_POST['points_per_credit'])) throw new Exception('Points per credit must be a valid number.');

		if(!check($_POST['daily_login_points'])) throw new Exception('You must provide daily login points.');
		if(!Validator::UnsignedNumber($_POST['daily_login_points'])) throw new Exception('Daily login points must be a valid number.');

		$addSeason = $db->query(
			"INSERT INTO `aioncms`.`battlepass_seasons`
			 (`name`, `description`, `start_date`, `end_date`, `status`, `points_per_credit`, `daily_login_points`)
			 VALUES (?, ?, ?, ?, ?, ?, ?)",
			array(
				$_POST['season_name'],
				check($_POST['season_description']) ? $_POST['season_description'] : '',
				$start_date,
				$end_date,
				0,
				$_POST['points_per_credit'],
				$_POST['daily_login_points']
			)
		);
		if(!$addSeason) throw new Exception('There was an error adding the season, try again.');

		# Optionally create 100 blank tiers
		if(check($_POST['create_tiers'])) {
			$newSeasonId = $db->lastInsertId();
			for($i = 1; $i <= 100; $i++) {
				$db->query(
					"INSERT INTO `aioncms`.`battlepass_tiers`
					 (`season_id`, `tier_number`, `required_points`, `reward_type`)
					 VALUES (?, ?, ?, 'item')",
					array($newSeasonId, $i, ($i * 100))
				);
			}
		}

		message('Season added successfully!', 'success');
		redirect('battlepass/admin/');

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

# ---- TOGGLE STATUS ----
if(check($_GET['id'], $_GET['toggle'])) {
	try {
		if(!Validator::UnsignedNumber($_GET['id'])) throw new Exception('The provided season id is not valid.');
		$toggleSeason = $db->query("UPDATE `aioncms`.`battlepass_seasons` SET `status` = 1 - `status` WHERE `id` = ?", array($_GET['id']));
		if(!$toggleSeason) throw new Exception('There was an error editing the season, try again.');
		redirect('battlepass/admin/');
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

# ---- DELETE SEASON ----
if(check($_GET['id'], $_GET['delete'])) {
	try {
		if(!Validator::UnsignedNumber($_GET['id'])) throw new Exception('The provided season id is not valid.');
		
		$db->query("DELETE FROM `aioncms`.`battlepass_tiers` WHERE `season_id` = ?", array($_GET['id']));
		$db->query("DELETE FROM `aioncms`.`battlepass_claims` WHERE `season_id` = ?", array($_GET['id']));
		$db->query("DELETE FROM `aioncms`.`battlepass_progress` WHERE `season_id` = ?", array($_GET['id']));
		$db->query("DELETE FROM `aioncms`.`battlepass_points_log` WHERE `season_id` = ?", array($_GET['id']));

		$deleteSeason = $db->query("DELETE FROM `aioncms`.`battlepass_seasons` WHERE `id` = ?", array($_GET['id']));
		if(!$deleteSeason) throw new Exception('There was an error deleting the season, try again.');

		message('Season deleted successfully!', 'success');
		redirect('battlepass/admin/');

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

echo '<div class="row">';

	# LEFT — SEASON LIST
	echo '<div class="col-md-8">';
		echo '<div class="block">';
			echo '<div class="block-content">';

				$seasons = $db->queryFetch("SELECT * FROM `aioncms`.`battlepass_seasons` ORDER BY `id` DESC");

				echo '<h3>Seasons</h3>';
				echo '<table class="table table-hover">';
				echo '<thead>';
					echo '<tr>';
						echo '<th>#</th>';
						echo '<th>Name</th>';
						echo '<th>Start</th>';
						echo '<th>End</th>';
						echo '<th>Status</th>';
						echo '<th style="width:230px;"></th>';
					echo '</tr>';
				echo '</thead>';
				echo '<tbody>';
				if(is_array($seasons)) {
					foreach($seasons as $season) {

						echo '<tr>';
							echo '<td>'.$season['id'].'</td>';
							echo '<td>'.htmlspecialchars($season['name']).'</td>';
							echo '<td>'.date('Y-m-d H:i', strtotime($season['start_date'])).'</td>';
							echo '<td>'.date('Y-m-d H:i', strtotime($season['end_date'])).'</td>';
							if($season['status'] == 1) {
								echo '<td><span class="label label-success">Active</span></td>';
							} else {
								echo '<td><span class="label label-default">Inactive</span></td>';
							}
							echo '<td>';
								// FIX: Nutze GET Parameter, aber über den Router-Pfad (ohne .php), damit AionCMS es frisst
								echo '<a href="'.__BASE_URL__.'battlepass/admintiers/?season='.$season['id'].'" class="btn btn-info btn-xs">Tiers</a> ';
								echo '<a href="'.__BASE_URL__.'battlepass/adminlogs/?season='.$season['id'].'" class="btn btn-default btn-xs">Logs</a> ';
								echo '<a href="'.__BASE_URL__.'battlepass/admin/id/'.$season['id'].'/toggle/1/" class="btn btn-warning btn-xs">'.($season['status'] == 1 ? 'Disable' : 'Enable').'</a> ';
								echo '<a href="'.__BASE_URL__.'battlepass/admin/id/'.$season['id'].'/delete/1/" class="btn btn-danger btn-xs" onclick="return confirm(\'Delete this season and ALL related data? This cannot be undone.\');">Delete</a>';
							echo '</td>';
						echo '</tr>';

					}
				} else {
					echo '<tr><td colspan="6">There are no seasons yet.</td></tr>';
				}
				echo '</tbody>';
				echo '</table>';

			echo '</div>';
		echo '</div>';
	echo '</div>';

	# RIGHT — ADD SEASON FORM
	echo '<div class="col-md-4">';
		echo '<div class="block">';
			echo '<div class="block-content">';

				echo '<h4>Add Season</h4>';
				echo '<form action="'.__BASE_URL__.'battlepass/admin/" method="post">';
					echo '<div class="form-group">';
						echo '<label>Name</label>';
						echo '<input type="text" name="season_name" class="form-control" placeholder="Season 1" required>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label>Description</label>';
						echo '<textarea name="season_description" class="form-control" style="height:80px;"></textarea>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label>Start Date</label>';
						echo '<input type="datetime-local" name="season_start" class="form-control" required>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label>End Date</label>';
						echo '<input type="datetime-local" name="season_end" class="form-control" required>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label>Points per Credit (Shop)</label>';
						echo '<input type="text" name="points_per_credit" class="form-control" value="1" required>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label>Daily Login Points</label>';
						echo '<input type="text" name="daily_login_points" class="form-control" value="10" required>';
					echo '</div>';
					echo '<div class="form-group">';
						echo '<label><input type="checkbox" name="create_tiers" value="1" checked> Create 100 blank tiers</label>';
					echo '</div>';
					echo '<button type="submit" class="btn btn-success" name="add_season" value="ok">Add Season</button>';
				echo '</form><br />';

				echo '<p>By default new seasons will be added with their status disabled. Enable them once you have configured their tiers and rewards.</p>';

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';