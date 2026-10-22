<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block votenreward"></div>
<br /><br />

<h3>Vote Rankings</h3>
<p>Get even more rewards by staying at the top of the monthly voting rankings.</p>

<br /><br />

<h4>How it works:</h4>
<ol>
	<li>Select your main character (will be shown in the rankings)</li>
	<li>Opt-in to participate (you have to opt-in each month)</li>
	<li>Vote!</li>
</ol>

<br /><br />

<h4>Rewards (<?php echo date("F"); ?>):</h4>
<table class="table table-bordered table-striped">
	<tr>
		<td>1st place</td>
		<td>
			300 Webshop Coins<br /><br />
		</td>
	</tr>
	<tr>
		<td>2nd place</td>
		<td>
			200 Webshop Coins<br /><br />
		</td>
	</tr>
	<tr>
		<td>3rd place</td>
		<td>
			100 Webshop Coins<br /><br />
		</td>
	</tr>
</table>

<br /><br />

<div class="col-md-6 col-md-offset-3 text-center">
	<?php
	try {
		
		$currentYear = (int) date("Y");
		$currentMonth = (int) date("m");
		$currentDay = (int) date("d");
		
		$db = Handler::loadDB();
		$sdb = Handler::loadDB('siel');
		
		$sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($_SESSION['userid']));
		
		$checkVoteCount = $db->queryFetchSingle("SELECT * FROM `aioncms`.`votes_count` WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
		if(is_array($checkVoteCount) && check($checkVoteCount['character'])) {
			
			# ALREADY PARTICIPATING
			
			echo '<h4>You are in!</h4>';
			echo '<h6>here are your votes so far</h6>';
			echo '<br />';
			echo '<br />';
			echo '<p>'.number_format($checkVoteCount['votes']).' votes</p>';
			
		} else {
			
			# NOT PARTICIPATING
			
			echo '<h4>Lets get started!</h4>';
			echo '<h6>choose your main character</h6>';
			echo '<br />';
			
			if(!is_array($sielCharacters)) {
				throw new Exception('You don\'t have any characters in SIEL.');
			}
			
			# opt-in process
			if(check($_POST['character_submit'])) {
				try {
					if(!check($_POST['character_name'])) throw new Exception('The character you selected is not valid.');
					
					$playerInfo = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `name` = ? AND `account_id` = ?", array($_POST['character_name'], $_SESSION['userid']));
					if(!is_array($playerInfo)) throw new Exception('The character you selected is not valid. [NiA]');
					
					$voteCount = $db->queryFetchSingle("SELECT * FROM `aioncms`.`votes_count` WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
					if(!is_array($voteCount)) {
						# not in vote count table
						if($currentDay > 5) throw new Exception('Sorry! You can only opt-in within the first 5 days of each month.');
						
						$optIn = $db->query("INSERT INTO `aioncms`.`votes_count` (`id`, `year`, `month`, `character`, `last_update`) VALUES (?, ?, ?, ?, now())", array($_SESSION['userid'], $currentYear, $currentMonth, $playerInfo['name']));
						if(!$optIn) throw new Exception('There was an error, please contact support. [F-OI]');
						
						# load info again
						$voteCount = $db->queryFetchSingle("SELECT * FROM `aioncms`.`votes_count` WHERE `id` = ? AND `year` = ? AND `month` = ?", array($_SESSION['userid'], $currentYear, $currentMonth));
					}
					
					if(!check($voteCount['character'])) {
						# update character name
						$updateCharacter = $db->query("UPDATE `aioncms`.`votes_count` SET `character` = ?, `last_update` = now() WHERE `id` = ? AND `year` = ? AND `month` = ?", array($playerInfo['name'], $_SESSION['userid'], $currentYear, $currentMonth));
						if(!$updateCharacter) throw new Exception('There was an error, please contact support. [F-UCN]');
					}
					
					redirect('usercp/voteranking/');
					
				} catch(Exception $ex) {
					message($ex->getMessage(), 'warning');
				}
			}
			
			# form
			echo '<form action="" method="post">';
				echo '<div class="form-group">';
					echo '<select class="form-control" name="character_name">';
						if(is_array($sielCharacters)) {
							foreach($sielCharacters as $player) {
								echo '<option value="'.$player['name'].'">'.$player['name'].'</option>';
							}
						}
					echo '</select>';
				echo '</div>';
				echo '<button type="submit" class="btn btn-primary btn-block" name="character_submit" value="1">Opt-in</button>';
			echo '</form>';
		
		}
		

	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
	?>
</div>