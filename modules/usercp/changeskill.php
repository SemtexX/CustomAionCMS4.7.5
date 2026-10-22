<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block itemenchant"></div>
<br /><br />

<h3>Item Enchantment: Change Skill</h3>
<p>You may change your item's breakthrough skill using this tool.</p>
<br />

<?php

if(!check($_GET['server'])) redirect('usercp/');
if(!check($_GET['player'])) redirect('usercp/');
if(!check($_GET['item'])) redirect('usercp/');

try {
	
	//if(!$_SESSION['is_staff']) throw new Exception('Try again later.');
	
	if(!isServerValid($_GET['server'])) throw new Exception('Your request could not be completed, please try again later.');
	
	# load server database
	$sdb = Handler::loadDB('siel');
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	
	# player data
	$playerData = $sdb->queryFetchSingle("SELECT * FROM `players` WHERE `account_id` = ? AND `name` = ?", array($accountData['id'], $_GET['player']));
	if(!is_array($playerData)) throw new Exception('Your request could not be completed, please try again later. [2]');
	
	# check item
	$itemData = $sdb->queryFetchSingle("SELECT * FROM `inventory` WHERE `item_owner` = ? AND `is_equiped` = ? AND `item_unique_id` = ?", array($playerData['id'], 0, $_GET['item']));
	if(!is_array($itemData)) throw new Exception('Oops! we could not find this item in your inventory!');
	
	# check if item is +20
	if($itemData['enchant'] != 20) throw new Exception('This item doesn\'t have a breakthrough skill yet.');
	
	# configs
	$enchantableItems = config('enchantable_items', true);
	$enchantablePrice = config('enchant_price', true);
	$skillChangePrice = config('enchant_skill_change_price', true);
	
	# is item enchantable? (in the list)
	if(!array_key_exists($itemData['item_id'], $enchantableItems)) throw new Exception('Sorry! this item\'s skill can\'t be changed.');

	# item name
	$itemName = getItemName($itemData['item_id']);
	if(!check($itemName)) throw new Exception('Sorry! we couldn\'t identify this item, please contact support.');
	
	$itemSkills = $enchantableItems[$itemData['item_id']];
	if(!is_array($itemSkills)) throw new Exception('Sorry! we couldn\'t identify this item, please contact support. [2]');
	
	
	if(check($_POST['submit_skillchange'])) {
		try {
			
			# check online
			if(isOnline($_SESSION['userid'], 'all')) throw new Exception('Your account is online, please disconnect.');
			
			# check credits
			if($accountData['toll'] < $skillChangePrice) throw new Exception('Sorry! you don\'t have anough credits to change the skill of this item.');
			
			# choose random skill
			$possibleSkills = array();
			
			if($itemData['buff_skill'] != $itemSkills[0]) $possibleSkills[] = $itemSkills[0];
			if($itemData['buff_skill'] != $itemSkills[1]) $possibleSkills[] = $itemSkills[1];
			if($itemData['buff_skill'] != $itemSkills[2]) $possibleSkills[] = $itemSkills[2];
			
			$randomSkill = $possibleSkills[mt_rand(0, 1)];
			if(!check($randomSkill)) throw new Exception('There was a problem enchanting your item, please contact support. [0]');
			
			# change item skill
			$changeSkill = $sdb->query("UPDATE `inventory` SET `buff_skill` = ? WHERE `item_unique_id` = ? AND `enchant` = 20 AND `is_amplified` = 1", array($randomSkill, $itemData['item_unique_id']));
			
			# remove old BT skill from player
			$removeOldSkill = $sdb->query("DELETE FROM `player_skills` WHERE `player_id` = ? AND `skill_id` = ?", array($playerData['id'], $itemData['buff_skill']));
			
			# error in query
			if(!$changeSkill) throw new Exception('There was a problem changing the skill of your item, please contact support.');
			
			# deduct credits
			$subtractCredits = $Account->subtractCredits($skillChangePrice);
			if(!$subtractCredits) throw new Exception('An error ocurred, please contact the Administrator. [E-CS]');
			
			# success message
			logSystem::add('item skill changed to ' . $randomSkill);
			$newSkillName = getSkillName($randomSkill);
			message('Your item\'s skill has been successfully changed to <strong>'.$newSkillName.'</strong>! You may now proceed to your <a href="'.module_url('usercp/inventory/server/'.$_GET['server'].'/player/'.$_GET['player'].'/', true).'" style="font-weight:bold;">inventory</a>.', 'success');
			
			
			
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	} else {
		
		echo '<div class="col-md-10 col-md-offset-1 text-center">';
			echo '<div class="panel panel-default">';
				echo '<div class="panel-body">';
					
					echo '<h2>'.$itemName.' +'.$itemData['enchant'].'</h2>';
					echo '<h4>'.$_GET['player'].' ('.$_GET['server'].')</h4>';
					echo '<br />';
					
					echo '<p>cost</p>';
					echo '<h3>'.$skillChangePrice.' credits</h3>';
					echo '<br />';
					
					echo '<p>100% success rate</p>';
					echo '<p>Make sure your account is offline before enchanting.</p>';
					echo '<br />';
					
					$skill_1 = getSkillName($itemSkills[0]);
					$skill_2 = getSkillName($itemSkills[1]);
					$skill_3 = getSkillName($itemSkills[2]);
					
					echo '<p>This item\'s skill will be changed to one of the following:</p>';
					if($itemData['buff_skill'] != $itemSkills[0]) echo '<p style="font-weight:bold;color:#73009e;">'.$skill_1.'</p>';
					if($itemData['buff_skill'] != $itemSkills[1]) echo '<p style="font-weight:bold;color:#73009e;">'.$skill_2.'</p>';
					if($itemData['buff_skill'] != $itemSkills[2]) echo '<p style="font-weight:bold;color:#73009e;">'.$skill_3.'</p>';
					echo '<br />';
					
					echo '<form action="" method="post">';
						echo '<button type="submit" name="submit_skillchange" value="ok" class="btn btn-success">Change Skill</button>';
					echo '</form>';
					
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}





