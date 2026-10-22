<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

message('
<strong>Regular:</strong> Can be redeemed by everyone once.<br />
<strong>Limited:</strong> Can be used by everyone until the use limit is reached.<br />
<strong>Account:</strong> Can be used by a single user once.<br />
', 'info');

if(check($_GET['disable'])) {
	try {
		
		$RedeemCodeDisable = new RedeemCode();
		$RedeemCodeDisable->disableCode($_GET['disable']);
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

if(check($_POST['code_submit'])) {
	try {
		
		$RedeemCodeAdd = new RedeemCode();
		$RedeemCodeAdd->setCode($_POST['code_value']);
		$RedeemCodeAdd->setCodeType($_POST['code_type']);
		$RedeemCodeAdd->setLimit($_POST['code_limit']);
		$RedeemCodeAdd->setUser($_POST['code_user']);
		$RedeemCodeAdd->setReward($_POST['code_reward']);
		$RedeemCodeAdd->addRewardCode();
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
	}
}

$RedeemCode = new RedeemCode();
$codesList = $RedeemCode->getRedeemCodesList();

echo '<table class="table">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>Id</th>';
			echo '<th>Code</th>';
			echo '<th>Type</th>';
			echo '<th>Limit</th>';
			echo '<th>User</th>';
			echo '<th>Reward</th>';
			echo '<th>Status</th>';
			echo '<th>Actions</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
		
		echo '<form action="" method="post">';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><input class="form-control" type="text" name="code_value" /></td>';
			echo '<td><select class="form-control" name="code_type"><option value="regular">Regular</option><option value="limited">Limited</option><option value="account">Account</option></select></td>';
			echo '<td><input class="form-control" type="text" name="code_limit" /></td>';
			echo '<td><input class="form-control" type="text" name="code_user" /></td>';
			echo '<td><input class="form-control" type="text" name="code_reward" /></td>';
			echo '<td colspan="2"><button type="submit" name="code_submit" value="1" class="btn btn-success">Add Code</button></td>';
		echo '</tr>';
		echo '</form>';
	
	if(is_array($codesList)) {
		foreach($codesList as $codeData) {
			
			$status = $codeData['status'] == 1 ? '<span class="label label-danger">expired</span>' : '<span class="label label-success">active</span>';
			echo '<tr>';
				echo '<td>'.$codeData['id'].'</td>';
				echo '<td>'.$codeData['redeem_code'].'</td>';
				echo '<td>'.$codeData['redeem_type'].'</td>';
				echo '<td>'.$codeData['redeem_limit'].'</td>';
				echo '<td>'.$codeData['redeem_user'].'</td>';
				echo '<td>'.$codeData['redeem_credit_amount'].'</td>';
				echo '<td>'.$status.'</td>';
				echo '<td><a href="'.__BASE_URL__.'admin/redeemcodes/disable/'.$codeData['id'].'" class="btn btn-default btn-xs">disable</a></td>';
			echo '</tr>';
		}
	}
	echo '</tbody>';
echo '</table>';