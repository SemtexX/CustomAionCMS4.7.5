<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<div class="page-header-block accsecurity"></div>
<br /><br />

<h3>Security PIN</h3>
<p>Configuring the security PIN will help you confirm ownership of your account.</p>
<br />

<?php
try {
	
	$Account = new Account();
	$Account->setId($_SESSION['userid']);
	$accountData = $Account->getAccountData();
	$accountSecurity = $Account->getExtraSecurityData();
	
	if(!is_array($accountData)) throw new Exception('Could not load your account\'s information.');
	if(check($accountSecurity['security_pin'])) throw new Exception('You have already set your security pin.');
	
	if(check($_POST['sp_submit'])) {
		try {
			# filters
			if(!check($_POST['sp_pin'])) throw new Exception('Please fill all the required fields.');
			if(!Validator::Length($_POST['sp_pin'], 4, 4)) throw new Exception('Your security PIN must contain 4 digits.');
			if(!Validator::UnsignedNumber($_POST['sp_pin'])) throw new Exception('Your security PIN must contain 4 digits.');

			# save pin
			$savePin = $Account->setSecurityPIN($_POST['sp_pin']);
			if(!$savePin) throw new Exception("Your request could not be completed. If this problem persists contact the administrator. [E-A004]");
			
			logSystem::add('set security pin');
			redirect('usercp/account/');
		} catch(Exception $ex) {
			message($ex->getMessage(), 'error');
			logSystem::add('set security pin error', 5);
		}
	}
	
	echo '<form action="'.module_url('usercp/securitypin/', true).'" method="post">';
	echo '<table class="my-account-table">';
		echo '<tr>';
			echo '<td>Security PIN:</td>';
			echo '<td><input type="text" name="sp_pin" class="form-control" placeholder="1234..." maxlength="4" autofocus/></td>';
		echo '</tr>';
		
		echo '<tr>';
			echo '<td></td>';
			echo '<td><button type="submit" name="sp_submit" value="ok" class="btn btn-primary">Save Security PIN</button></td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
	
} catch(Exception $ex) {
	message($ex->getMessage(), 'error');
}
?>