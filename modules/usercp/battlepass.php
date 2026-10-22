<?php
/**
 * AionCMS
 * https://aioncms.com
 *
 * BattlePass — UserCP page (mit integriertem Claim)
 */

// --- INTEGRIERTER CLAIM-PROZESS (Klassischer POST) ---
if (isset($_POST['action']) && $_POST['action'] === 'claim_reward') {
    try {
        if (!isLoggedIn()) throw new Exception('Not logged in.');
        
        // CSRF Prüfung
        if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token. Please refresh the page.');
        }
        if (empty($_POST['tier_id'])) throw new Exception('No tier specified.');
        if (empty($_POST['character'])) throw new Exception('No character specified.');

        // BattlePass laden
        if (!class_exists('BattlePass')) {
            $bpLib = __PATH_LIBRARIES__ . 'lib.battlepass.php';
            if (!@include_once($bpLib)) throw new Exception('Could not load BattlePass library.');
        }

        $BP = new BattlePass();
        $BP->setUserId($_SESSION['userid']);
        $BP->setServer('siel');

        if (!$BP->hasActiveSeason()) throw new Exception('No active season.');

        // Punkte abholen
        $msg = $BP->claimTier((int)$_POST['tier_id'], $_POST['character']);
        
        // Erfolg!
        message($msg, 'success');
        
    } catch (Exception $ex) {
        message($ex->getMessage(), 'error');
    }
}
// ------------------------------------------

?><div class="page-header-block usercp"></div>
<br /><br />

<h3>BattlePass</h3>
<p>Earn points by logging in daily and by purchasing items from the shop, then claim your rewards!</p>
<br />

<?php
try {

	// CSRF-Token generieren (PHP 5.4 kompatibel)
	if (empty($_SESSION['csrf_token'])) {
		if (function_exists('openssl_random_pseudo_bytes')) {
			$_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
		} else {
			$_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
		}
	}

	# BattlePass laden
	if(!class_exists('BattlePass')) {
		$bpLib = __PATH_LIBRARIES__ . 'lib.battlepass.php';
		if(!@include_once($bpLib)) {
			throw new Exception('Could not load BattlePass library.');
		}
	}

	$BP = new BattlePass();
	$BP->setUserId($_SESSION['userid']);
	$BP->setServer('siel');

	$accountData = $BP->getAccountData();
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');

	if(!$BP->hasActiveSeason()) {
		echo '<br />';
		message('There is currently no active BattlePass season. Please check back later!', 'warning');
		return;
	}

	# Tägliche Login-Punkte vergeben
	$BP->grantDailyLoginIfDue();

	$season  = $BP->getCurrentSeason();
	$tiers   = $BP->getTiers();
	$points  = $BP->getPoints();
	$curTier = $BP->getCurrentTierNumber();
	$claimed = $BP->getClaimedTierIds();

	# Charaktere laden
	$sdb = Handler::loadDB('siel');
	$characters = $sdb->queryFetch(
		"SELECT * FROM `players` WHERE `account_id` = ?",
		array($accountData['id'])
	);

	# -------- Season header --------
	echo '<div class="panel panel-default">';
		echo '<div class="panel-body text-center">';
			echo '<h2>'.htmlspecialchars($season['name']).'</h2>';
			if(check($season['description'])) {
				echo '<p style="color:#aaa;">'.htmlspecialchars($season['description']).'</p>';
			}
			echo '<p>Season ends on <strong>'.date('d.m.Y H:i', strtotime($season['end_date'])).'</strong></p>';
			echo '<br />';
			echo '<div class="row">';
				echo '<div class="col-md-4">';
					echo '<h4>Your Points</h4>';
					echo '<p style="font-size:24px;font-weight:bold;color:#C99B4B;">'.number_format($points).'</p>';
				echo '</div>';
				echo '<div class="col-md-4">';
					echo '<h4>Current Tier</h4>';
					echo '<p style="font-size:24px;font-weight:bold;color:#C99B4B;">'.$curTier.' / '.count($tiers).'</p>';
				echo '</div>';
				echo '<div class="col-md-4">';
					echo '<h4>Rewards Claimed</h4>';
					echo '<p style="font-size:24px;font-weight:bold;color:#C99B4B;">'.count($claimed).'</p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	# -------- Keine Charaktere Warnung --------
	if(!is_array($characters) || count($characters) === 0) {
		message('You do not have any characters on this server yet. Create one first in order to claim BattlePass rewards.', 'warning');
	}

	# -------- Tier grid --------
	echo '<br />';
	echo '<h4>Rewards</h4>';
	echo '<div class="row">';

	foreach($tiers as $t) {
		$tierNum   = (int)$t['tier_number'];
		$required  = (int)$t['required_points'];
		$isReached = $points >= $required;
		$isClaimed = in_array((int)$t['id'], array_map('intval', $claimed), true);

		# Belohnung / Icon
		$rewardIcon  = '';
		$rewardLabel = '';
		if($t['reward_type'] === 'item') {
			$rewardIcon  = $BP->getItemIconUrl((int)$t['reward_item_id']);
			$rewardLabel = (int)$t['reward_item_count'].'x '.$BP->getItemName((int)$t['reward_item_id']);
		} elseif($t['reward_type'] === 'credits') {
			$rewardLabel = number_format((int)$t['reward_credits']).' Credits';
		} else {
			$rewardLabel = $t['reward_label'];
		}

		$border = $isClaimed ? '#3c3' : ($isReached ? '#C99B4B' : '#444');

		echo '<div class="col-md-3 col-sm-4 col-xs-6" style="margin-bottom:20px;">';
			echo '<div style="border:2px solid '.$border.';border-radius:6px;padding:10px;text-align:center;background:#1e1e1e;">';

				echo '<div style="font-size:11px;color:#aaa;">Tier</div>';
				echo '<div style="font-size:22px;font-weight:bold;color:#C99B4B;">'.$tierNum.'</div>';

				if($rewardIcon !== '') {
					echo '<img src="'.htmlspecialchars($rewardIcon).'" style="max-width:48px;max-height:48px;margin:6px 0;" />';
				}

				echo '<div style="font-size:12px;color:#ddd;min-height:32px;">'.htmlspecialchars($rewardLabel).'</div>';
				echo '<div style="font-size:10px;color:#888;">'.$required.' points</div>';

				if($isClaimed) {
					echo '<div style="color:#3c3;font-weight:bold;margin-top:6px;">&#10003; Claimed</div>';
				} elseif($isReached) {
					// Der Button triggert ein verstecktes Formular
					echo '<button class="btn btn-success btn-sm" '
					   . 'onclick="document.getElementById(\'claimTierId\').value = '.(int)$t['id'].'; document.getElementById(\'charSelectModal\').style.display=\'block\';" '
					   . 'style="margin-top:6px;">Claim</button>';
				} else {
					echo '<div style="color:#777;margin-top:6px;">&#128274;</div>';
				}

			echo '</div>';
		echo '</div>';
	}

	echo '</div>';

	# -------- Claim Modal (reines HTML/CSS/JS, kein Bootstrap-JQuery nötig) --------
	echo '<div id="charSelectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999;">';
	echo '  <div style="background:#2c2c2c; width:400px; margin:100px auto; padding:20px; border-radius:8px; color:#fff;">';
	echo '      <h4>Claim Reward</h4>';
	echo '      <p>Select a character:</p>';
	echo '      <form method="POST" action="">';
	echo '          <input type="hidden" name="action" value="claim_reward">';
	echo '          <input type="hidden" name="tier_id" id="claimTierId" value="">';
	echo '          <input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
	echo '          <select name="character" style="width:100%; padding:10px; margin-bottom:15px; background:#1e1e1e; color:#fff; border:1px solid #555; border-radius:4px;">';
	if(is_array($characters)) {
		foreach($characters as $c) {
			echo '<option value="'.htmlspecialchars($c['name']).'">'.htmlspecialchars($c['name']).'</option>';
		}
	}
	echo '          </select>';
	echo '          <div style="text-align:right;">';
	echo '              <button type="button" onclick="document.getElementById(\'charSelectModal\').style.display=\'none\';" style="padding:8px 15px; background:#555; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-right:10px;">Cancel</button>';
	echo '              <button type="submit" style="padding:8px 15px; background:#4CAF50; color:#fff; border:none; border-radius:4px; cursor:pointer;">Confirm Claim</button>';
	echo '          </div>';
	echo '      </form>';
	echo '  </div>';
	echo '</div>';

} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>