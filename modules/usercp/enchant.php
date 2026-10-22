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

<h3>Item Enchantment</h3>
<p>Web-based tool for enchanting your items!</p>
<br />

<?php

if(!check($_GET['server'])) redirect('usercp/');
if(!check($_GET['player'])) redirect('usercp/');
if(!check($_GET['item'])) redirect('usercp/');

try {
	
	//if(!$_SESSION['is_staff']) throw new Exception('Try again later.');
	
	if(!isServerValid($_GET['server'])) throw new Exception('Your request could not be completed, please try again later.');
	
	# load site database
	$db = Handler::loadDB();
	
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
	
	# configs
	$enchantableItems = config('enchantable_items', true);
	$enchantablePrice = config('enchant_price', true);
	
	# is item enchantable?
	if(!array_key_exists($itemData['item_id'], $enchantableItems)) throw new Exception('Sorry! this item can\'t be enchanted.');
	
	# check enchant price
	if(!array_key_exists($itemData['enchant'], $enchantablePrice)) throw new Exception('Sorry! this item has reached the maximum enchantment level allowed through the website.');
	$enchantPrice = $enchantablePrice[$itemData['enchant']];
	
	# VIP discount
	if($accountData['membership'] == 2 && strtotime($accountData['expire']) > time()) {
		$enchantPrice = floor($enchantPrice-($enchantPrice*0.1));
	}
	
	# item name
	$itemName = getItemName($itemData['item_id']);
	if(!check($itemName)) throw new Exception('Sorry! we couldn\'t identify this item, please contact support.');
	
	$itemSkills = $enchantableItems[$itemData['item_id']];
	if(!is_array($itemSkills)) throw new Exception('Sorry! we couldn\'t identify this item, please contact support. [2]');
	
	$nextEnchantLevel = $itemData['enchant']+1;
	
	# check free tokens
	$useEnchantToken = false;
	$checkTokens = $db->queryFetchSingle("SELECT * FROM `aioncms`.`website_enchant_tokens` WHERE `account_id` = ?", array($accountData['id']));
	if(is_array($checkTokens)) {
		if($checkTokens['tokens'] >= 1) {
			$useEnchantToken = true;
		}
	}
	
	if(check($_POST['submit_enchant'])) {
		try {
			
			# check online
			if(isOnline($_SESSION['userid'], 'all')) throw new Exception('Your account is online, please disconnect.');
			
			# check credits
			if($useEnchantToken == false) if($accountData['toll'] < $enchantPrice) throw new Exception('Sorry! you don\'t have anough credits to enchant this item.');
			
			if($itemData['enchant'] == 19) {
				# choose random skill
				$randomSkill = $itemSkills[mt_rand(0, 2)];
				if(!check($randomSkill)) throw new Exception('There was a problem enchanting your item, please contact support. [0]');
				
				$enchant = $sdb->query("UPDATE `inventory` SET `enchant` = `enchant` + 1, `is_amplified` = 1, `buff_skill` = ? WHERE `item_unique_id` = ?", array($randomSkill, $itemData['item_unique_id']));
			} else {
				# regular enchant +1
				$enchant = $sdb->query("UPDATE `inventory` SET `enchant` = `enchant` + 1, `is_amplified` = 1 WHERE `item_unique_id` = ?", array($itemData['item_unique_id']));
			}
			
			# error in query
			if(!$enchant) throw new Exception('There was a problem enchanting your item, please contact support.');
			
			# deduct credits
			if($useEnchantToken == false) {
				$subtractCredits = $Account->subtractCredits($enchantPrice);
				if(!$subtractCredits) throw new Exception('An error ocurred, please contact the Administrator. [E-CS]');
			}
			
			# deduct token
			if($useEnchantToken == true) {
				$deductToken = $db->query("UPDATE `aioncms`.`website_enchant_tokens` SET `tokens` = `tokens` - 1 WHERE `account_id` = ?", array($accountData['id']));
				if(!$deductToken) throw new Exception('An error ocurred, please contact the Administrator. [E-TS]');
			}
			
			# success message
			if($useEnchantToken == true) {
				logSystem::add('item enchanted to +' . $nextEnchantLevel . ' (token)');
			} else {
				logSystem::add('item enchanted to +' . $nextEnchantLevel);
			}
			
			if($itemData['enchant'] == 19) {
				message('Your item has been successfully enchanted! You may now proceed to your <a href="'.module_url('usercp/inventory/server/'.$_GET['server'].'/player/'.$_GET['player'].'/', true).'" style="font-weight:bold;">inventory</a>.', 'success');
			} else {
				redirect('usercp/enchant/server/'.$_GET['server'].'/player/'.$_GET['player'].'/item/'.$itemData['item_unique_id'].'/');
			}
			
			
			
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
					if($useEnchantToken == true) {
						# use free token
						echo '<h3>FREE</h3>';
						echo '<p>(you have <strong>'.$checkTokens['tokens'].'</strong> tokens left)</p>';
						
					} else {
						# use credits
						if($accountData['membership'] == 2 && strtotime($accountData['expire']) > time()) {
							echo '<h3 style="color:#ffae00;">'.$enchantPrice.' credits<br />(VIP 10% off)</h3>';
						} else {
							echo '<h3>'.$enchantPrice.' credits</h3>';
						}
					}
					echo '<br />';
					
					echo '<p>100% success rate</p>';
					echo '<p>Make sure your account is offline before enchanting.</p>';
					echo '<br />';
					
					if($itemData['enchant'] == 19) {
						//echo '<p>This item will receive a random skill.</p>';
						
						$skill_1 = getSkillName($itemSkills[0]);
						$skill_2 = getSkillName($itemSkills[1]);
						$skill_3 = getSkillName($itemSkills[2]);
						
						echo '<p>This item will receive one of the following skills:</p>';
						echo '<p style="font-weight:bold;color:#73009e;">'.$skill_1.'</p>';
						echo '<p style="font-weight:bold;color:#73009e;">'.$skill_2.'</p>';
						echo '<p style="font-weight:bold;color:#73009e;">'.$skill_3.'</p>';
						echo '<br />';
					}
					
					echo '<form action="" method="post">';
						echo '<button type="submit" name="submit_enchant" value="ok" class="btn btn-success">Enchant to +'.$nextEnchantLevel.'</button>';
					echo '</form>';
					
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}





