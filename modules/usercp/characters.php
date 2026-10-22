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

<h3>My Characters</h3>
<br />
<?php
try {
	
	$sdb = Handler::loadDB('siel');
	
	$sielCharacters = $sdb->queryFetch("SELECT * FROM `players` WHERE `account_id` = ?", array($_SESSION['userid']));
	
	if(!is_array($sielCharacters)) {
		echo '<br />';
		message('Looks like you don\'t have any characters yet.', 'error');
	}
	
	# SIEL
	if(is_array($sielCharacters)) {
		foreach($sielCharacters as $character) {
			
			$characterLevel = expToLevel($character['exp']);
			$characterRace = getRaceImg($character['race']);
			$characterGender = getGenderImg($character['gender']);
			$characterClass = getClassImg($character['player_class']);
			$characterLocation = getLocationName($character['world_id']);
			$characterStatus = getOnlineStatusImg($character['online']);
			
			$characterLegionData = $sdb->queryFetchSingle("SELECT * FROM `legion_members` WHERE `player_id` = ?", array($character['id']));
			if(is_array($characterLegionData)) {
				$legionData = $sdb->queryFetchSingle("SELECT * FROM `legions` WHERE `id` = ?", array($characterLegionData['legion_id']));
				if(is_array($legionData)) {
					$characterLegion = $legionData['name'];
					$characterLegionProfileName = preg_replace('/\s+/', '-', trim($characterLegion));
					$characterLegionProfileLink = __BASE_URL__ . 'legion/' . $legionData['id'] . '/' . $characterLegionProfileName;
				}
			}
			
			echo '<div class="panel panel-default">';
				echo '<div class="panel-body">';
					echo '<div class="col-md-5">';
						echo '<span style="font-weight: bold;font-size: 24px;">'.$character['name'].'</span> ';
						echo $characterStatus;
						echo '<br /><span style="font-size: 11px;margin-bottom: 5px;">Level '.$characterLevel.'</span>';
						if($characterLegion) echo '<br /><span style="font-size: 11px;">Legion:</span>';
						if($characterLegion) echo '<br /><span><a href="'.$characterLegionProfileLink.'">'.$characterLegion.'</a></span> <a href="'.module_url('usercp/legionprofile/id/'.$legionData['id'], true).'" class="btn btn-xs btn-default">customize</a>';
						echo '<br /><span style="font-size: 11px;">Last seen at:</span>';
						echo '<br /><span>'.$characterLocation.'</span> <span style="font-size: 11px;font-weight:bold;">(SIEL)</span>';
					echo '</div>';
					echo '<div class="col-md-5">';
						echo '<div class="col-md-4">';
							echo $characterRace;
						echo '</div>';
						echo '<div class="col-md-4">';
							echo $characterGender;
						echo '</div>';
						echo '<div class="col-md-4">';
							echo $characterClass;
						echo '</div>';
						
						if($characterLevel >= 70 && $characterLevel < 75) {
							echo '<div class="col-md-12 text-center" style="padding-top: 10px;">';
								echo '<a href="'.module_url('usercp/boost/server/siel/player/'.$character['name'], true).'" class="btn btn-xs btn-danger">Level Boost</a>';
							echo '</div>';
						}
						
					echo '</div>';
					echo '<div class="col-md-2">';
						echo '<a href="'.module_url('usercp/inventory/server/siel/player/'.$character['name'], true).'" class="btn btn-xs btn-block btn-primary">Inventory</a>';
						echo '<a href="'.module_url('usercp/unstuck/server/siel/player/'.$character['name'], true).'" class="btn btn-xs btn-block btn-primary">Unstick</a>';
						//echo '<a href="'.module_url('usercp/transfer/server/siel/player/'.$character['name'], true).'" class="btn btn-xs btn-block btn-primary">Transfer</a>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		}
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}