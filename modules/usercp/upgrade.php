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

<h3>Upgrade Account</h3>

<?php
try {
	
	// 1= premium
	// 2= vip
	// 0= normal
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	
	$premiumCost = config('premium_cost', true);
	$vipCost = config('vip_cost', true);
	
	
	echo '<p>You currently have <strong>'.number_format($accountData['toll']).'</strong> credit(s). <a href="'.module_url('donate/', true).'" class="btn btn-success btn-xs">add credits</a></p><br />';
	
	if(check($_GET['type'])) {
		try {
			switch($_GET['type']) {
				case 'premium':
					# GET PREMIUM
					if($premiumCost > $accountData['toll']) throw new Exception('You don\'t have enough credits.');
					if($accountData['membership'] == 1) throw new Exception('Your account already has premium membership.');
					if($accountData['old_membership'] == 1) throw new Exception('Your account already has premium membership.');
					
					$upgradeAccount = $Account->setPremium();
					if(!$upgradeAccount) throw new Exception('Your account could not be upgraded, please contact the Administrator.');
					
					$subtractCredits = $Account->subtractCredits($premiumCost);
					if(!$subtractCredits) throw new Exception('An error ocurred, please contact the Administrator.');
					
					logSystem::add('upgraded membership (premium)');
					
					redirect('usercp/upgrade/');
					break;
				case 'vip':
					# GET VIP
					if($vipCost > $accountData['toll']) throw new Exception('You don\'t have enough credits.');
					
					$upgradeAccount = $Account->setVip(30);
					if(!$upgradeAccount) throw new Exception('Your account could not be upgraded, please contact the Administrator.');
					
					$subtractCredits = $Account->subtractCredits($vipCost);
					if(!$subtractCredits) throw new Exception('An error ocurred, please contact the Administrator.');
					
					logSystem::add('upgraded membership (vip)');
					
					redirect('usercp/upgrade/');
					break;
				default:
					throw new Exception('Your request could not be completed.');
			}
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
		}
	}
} catch(Exception $ex) {
	redirect('usercp/');
}
?>
<div class="upgrade_premium">
	<span style="color:#00ffc6;font-size:24px;font-weight:bold;">PREMIUM MEMBERSHIP</span><br />
	<span style="color:#ffffff;font-size: 18px;font-weight:bold;"><?php echo number_format($premiumCost); ?> Credits | Lifetime</span>
	
	<br /><br />
	<span style="font-weight:bold;color:#51b19c;">2x - 10x</span> Progresive Exp Rates<br />
	<span style="font-weight:bold;color:#51b19c;">7x</span> Drop<br />
	<span style="font-weight:bold;color:#51b19c;">3x - 11x</span> Group Exp<br />
	<span style="font-weight:bold;color:#51b19c;">6x</span> Gathering Exp<br />
	<span style="font-weight:bold;color:#51b19c;">6x</span> Crafting Exp<br />
	<span style="font-weight:bold;color:#51b19c;">2x - 10x</span> Quest Exp<br />
	<span style="font-weight:bold;color:#51b19c;">5x</span> Kinah Rate<br />
	<span style="font-weight:bold;color:#51b19c;">5x</span> Abyss points Rate<br />
	<span style="font-weight:bold;color:#51b19c;">7x</span> Pet Feeding Rate<br />
	<span style="font-weight:bold;color:#51b19c;">2x</span> Gathering Rate<br />
	<!--<span style="font-weight:bold;color:#51b19c;">4x</span> Sell Limit<br />
	<span style="font-weight:bold;color:#51b19c;">1.75x</span> PvP Arena Reward<br />
	<span style="font-weight:bold;color:#51b19c;">20x</span> Crit Craft Rate<br />
	<span style="font-weight:bold;color:#51b19c;">30x</span> Combo Craft Rate<br />-->
	<span style="font-weight:bold;color:#51b19c;">New</span> Character Profiles<br />
	<span style="font-weight:bold;color:#51b19c;">New</span> Legion Profile<br />
	
	<br />
	<?php
	if($accountData['membership'] == 1) {
		# has premium
		echo '<span style="font-size: 18px;font-weight:bold;color:#9effea;">Your account has Premium membership!</span>';
	} else {
		if($accountData['old_membership'] == 1) {
			# has premium
			echo '<span style="font-size: 18px;font-weight:bold;color:#9effea;">Your account has Premium membership!</span>';
			
		} else {
			# not premium
			echo '<a href="#" class="btn btn-default" data-toggle="modal" data-target="#upgradePremium">Upgrade Account</a>';
		}
	}
	?>
	
</div>

<div class="upgrade_vip">
	<span style="color:#ffcc00;font-size:24px;font-weight:bold;">V.I.P. MEMBERSHIP</span><br />
	<span style="color:#ffffff;font-size: 18px;font-weight:bold;"><?php echo number_format($vipCost); ?> Credits | 30 Days</span>
	
	<br /><br />
	<span style="font-weight:bold;color:#b19351;">5x - x15</span> Progresive Exp Rates<br />
	<span style="font-weight:bold;color:#b19351;">10x</span> Drop<br />
	<span style="font-weight:bold;color:#b19351;">6x - x16</span> Progresive Group Exp Rates<br />
	<span style="font-weight:bold;color:#b19351;">8x</span> Gathering Exp<br />
	<span style="font-weight:bold;color:#b19351;">8x</span> Crafting Exp<br />
	<span style="font-weight:bold;color:#b19351;">5x - 15x</span> Progresive Quest Exp Rates<br />
	<span style="font-weight:bold;color:#b19351;">10x</span> Kinah Rate<br />
	<span style="font-weight:bold;color:#b19351;">10x</span> Abyss points Rate<br />
	<span style="font-weight:bold;color:#b19351;">10x</span> Pet Feeding Rate<br />
	<span style="font-weight:bold;color:#b19351;">3x</span> Gathering Rate<br />
	<!--<span style="font-weight:bold;color:#b19351;">6x</span> Sell Limit<br />
	<span style="font-weight:bold;color:#b19351;">2x</span> PvP Arena Reward<br />
	<span style="font-weight:bold;color:#b19351;">25x</span> Crit Craft Rate<br />
	<span style="font-weight:bold;color:#b19351;">35x</span> Combo Craft Rate<br />-->
	<span style="font-weight:bold;color:#b19351;">New</span> Web Enchantment Tool<br />
	<span style="font-weight:bold;color:#b19351;">1.5x</span> Voting Rewards<br />
	
	<br />
	
	<!--<span style="font-weight:bold;">* All items tradeable with VIP Membership using the <a href="https://aioncms.com/" target="#"> Item Pak</a> *</span>-->
	
	<br /><br />
	<?php
	if($accountData['membership'] == 2) {
		# has vip
		echo '<span style="font-size: 18px;font-weight:bold;color:#fff79e;">Your account has VIP membership!</span><br />';
		echo '<span style="font-size: 18px;font-weight:bold;">Expiration Date: '.$accountData['expire'].'</span><br /><br />';
		echo '<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#extendVip">Extend</a>';
	} else {
		if($accountData['old_membership'] == 2) {
			# has vip
			echo '<span style="font-size: 18px;font-weight:bold;color:#fff79e;">Your account has VIP membership!</span><br />';
			echo '<span style="font-size: 18px;font-weight:bold;">Expiration Date: '.$accountData['expire'].'</span><br /><br />';
			echo '<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#extendVip">Extend</a>';
			
		} else {
			# not vip
			echo '<a href="#" class="btn btn-warning" data-toggle="modal" data-target="#upgradeVip">Upgrade Account</a>';
		}
	}
	?>
	
</div>

<!-- Premium -->
<div class="modal fade" id="upgradePremium" tabindex="-1" role="dialog" aria-labelledby="upgradePremiumConfirm">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="upgradePremiumConfirm">Upgrade to Premium</h4>
			</div>
			<div class="modal-body">
				Your credits will be deducted once you upgrade your account. To proceed please click the "Upgrade" button below.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<a href="<?php module_url('usercp/upgrade/type/premium/'); ?>" class="btn btn-success">Upgrade</a>
			</div>
		</div>
	</div>
</div>

<!-- Vip -->
<div class="modal fade" id="upgradeVip" tabindex="-1" role="dialog" aria-labelledby="upgradeVipConfirm">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="upgradeVipConfirm">Upgrade to V.I.P.</h4>
			</div>
			<div class="modal-body">
				Your credits will be deducted once you upgrade your account. To proceed please click the "Upgrade" button below.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<a href="<?php module_url('usercp/upgrade/type/vip/'); ?>" class="btn btn-success">Upgrade</a>
			</div>
		</div>
	</div>
</div>

<!-- Vip (extend) -->
<div class="modal fade" id="extendVip" tabindex="-1" role="dialog" aria-labelledby="extendVipConfirm">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="extendVipConfirm">Extend V.I.P. by 30 days.</h4>
			</div>
			<div class="modal-body">
				Your credits will be deducted once you extend your V.I.P. membership. To proceed please click the "Extend" button below.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				<a href="<?php module_url('usercp/upgrade/type/vip/'); ?>" class="btn btn-success">Extend</a>
			</div>
		</div>
	</div>
</div>