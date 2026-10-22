<?php
/**
 * AionCMS
 * https://aioncms.com
 *
 * BattlePass — Admin: Tier editor for a season
 */

if(!check($_GET['season'])) {
    echo '<div class="alert alert-warning">Please select a season from the <a href="'.__BASE_URL__.'battlepass/admin/">BattlePass Admin</a> list.</div>';
    return;
}
if(!Validator::UnsignedNumber($_GET['season'])) throw new Exception('The provided season id is not valid.');

$seasonId = (int)$_GET['season'];

$seasonInfo = $db->queryFetchSingle("SELECT * FROM `aioncms`.`battlepass_seasons` WHERE `id` = ?", array($seasonId));
if(!is_array($seasonInfo)) throw new Exception('The provided season id is not valid.');

# ---- SAVE TIERS ----
if(check($_POST['save_tiers'])) {
	try {

		if(is_array($_POST['tiers'])) {

			foreach($_POST['tiers'] as $tierId => $data) {

				if(!Validator::UnsignedNumber($tierId)) continue;

				$requiredPoints = isset($data['required_points']) ? (int)$data['required_points'] : 0;
				$rewardType     = isset($data['reward_type']) ? $data['reward_type'] : 'item';
				$rewardItemId   = isset($data['reward_item_id']) ? (int)$data['reward_item_id'] : 0;
				$rewardItemCnt  = isset($data['reward_item_count']) ? (int)$data['reward_item_count'] : 1;
				$rewardItemEnch = isset($data['reward_item_enchant']) ? (int)$data['reward_item_enchant'] : 0;
				$rewardCredits  = isset($data['reward_credits']) ? (int)$data['reward_credits'] : 0;
				$rewardLabel    = isset($data['reward_label']) ? $data['reward_label'] : '';
				$rewardValue    = isset($data['reward_value']) ? $data['reward_value'] : '';

				if(!in_array($rewardType, array('item', 'credits', 'title', 'web_badge'))) continue;

				$editTier = $db->query(
					"UPDATE `aioncms`.`battlepass_tiers` SET
						`required_points`     = ?,
						`reward_type`         = ?,
						`reward_item_id`      = ?,
						`reward_item_count`   = ?,
						`reward_item_enchant` = ?,
						`reward_credits`      = ?,
						`reward_label`        = ?,
						`reward_value`        = ?
					 WHERE `id` = ? AND `season_id` = ?",
					array(
						$requiredPoints,
						$rewardType,
						$rewardItemId,
						$rewardItemCnt,
						$rewardItemEnch,
						$rewardCredits,
						$rewardLabel,
						$rewardValue,
						$tierId,
						$seasonId
					)
				);
				if(!$editTier) throw new Exception('There was an error editing one of the tiers, try again.');
			}

			message('Tiers saved successfully!', 'success');
			redirect('battlepass/admintiers/?season='.$seasonId);
		}

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

$tiers = $db->queryFetch("SELECT * FROM `aioncms`.`battlepass_tiers` WHERE `season_id` = ? ORDER BY `tier_number` ASC", array($seasonId));

echo '<div class="row">';

	echo '<div class="col-md-12">';
		echo '<div class="block">';
			echo '<div class="block-content">';

				echo '<h3>Tiers &mdash; '.htmlspecialchars($seasonInfo['name']).'</h3>';
				echo '<p><a href="'.__BASE_URL__.'battlepass/admin/" class="btn btn-default btn-xs">&laquo; Back to Seasons</a></p>';

				echo '<form action="'.__BASE_URL__.'battlepass/admintiers/?season='.$seasonId.'" method="post">';
					echo '<table class="table table-hover">';
					echo '<thead>';
						echo '<tr>';
							echo '<th style="width:60px;">Tier</th>';
							echo '<th style="width:100px;">Points</th>';
							echo '<th style="width:110px;">Reward Type</th>';
							echo '<th style="width:110px;">Item ID</th>';
							echo '<th style="width:80px;">Count</th>';
							echo '<th style="width:70px;">Ench.</th>';
							echo '<th style="width:100px;">Credits</th>';
							echo '<th>Label</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';

					if(is_array($tiers)) {
						foreach($tiers as $tier) {

							$n = 'tiers['.$tier['id'].']';

							echo '<tr>';
								echo '<td><strong>'.$tier['tier_number'].'</strong></td>';

								echo '<td><input type="text" name="'.$n.'[required_points]" class="form-control" value="'.(int)$tier['required_points'].'"></td>';

								echo '<td>';
									echo '<select name="'.$n.'[reward_type]" class="form-control">';
										$types = array('item', 'credits', 'title', 'web_badge');
										foreach($types as $rt) {
											echo '<option value="'.$rt.'"'.($tier['reward_type'] === $rt ? ' selected' : '').'>'.$rt.'</option>';
										}
									echo '</select>';
								echo '</td>';

								echo '<td><input type="text" name="'.$n.'[reward_item_id]" class="form-control" value="'.(int)$tier['reward_item_id'].'"></td>';
								echo '<td><input type="text" name="'.$n.'[reward_item_count]" class="form-control" value="'.(int)$tier['reward_item_count'].'"></td>';
								echo '<td><input type="text" name="'.$n.'[reward_item_enchant]" class="form-control" value="'.(int)$tier['reward_item_enchant'].'"></td>';
								echo '<td><input type="text" name="'.$n.'[reward_credits]" class="form-control" value="'.(int)$tier['reward_credits'].'"></td>';
								echo '<td><input type="text" name="'.$n.'[reward_label]" class="form-control" value="'.htmlspecialchars($tier['reward_label']).'"></td>';
							echo '</tr>';

						}
					} else {
						echo '<tr><td colspan="8">This season has no tiers yet.</td></tr>';
					}

					echo '</tbody>';
					echo '</table>';

					echo '<button type="submit" class="btn btn-success" name="save_tiers" value="ok">Save All Tiers</button>';
				echo '</form>';

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';