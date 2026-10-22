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

<h3>Verify Email Address</h3>

<br />

<?php

if(!check($_GET['key'])) redirect();

# Change Password Process
if(check($_POST['acc_submit'], $_POST['acc_username'])) {
	
	try {
		
		$Account = new Account();
		$Account->setUsername($_POST['acc_username']);
		$Account->loadAccountDataFromUsername();
		$accountData = $Account->getAccountData();
		$extraSecurityData = $Account->getExtraSecurityData();
		
		if(!is_array($accountData)) throw new Exception('There was an error verifying your email, please contact support. [1]');
		
		# check if already verified
		if($extraSecurityData['email_confirmed'] == 1) throw new Exception('Your account\'s email address is already verified.');
		
		# compare keys
		$verificationCode = md5(md5('338nr72o3rcn8g32fx') . md5($accountData['name']));
		if($_GET['key'] != $verificationCode) throw new Exception('There was an error verifying your email, please contact support. [2]');
		
		# update account security
		$activateAccount = $Account->activateAccount();
		if(!$activateAccount) throw new Exception('There was an error verifying your email, please contact support. [3]');
		
		message('<strong>Awesome!</strong> Your account\'s email address has been successfully verified.', 'success');
		
		logSystem::add('email verification completed');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
		logSystem::add('email verification error', 5);
	}
	
} else {
	
	# enter username form
	echo '<form action="'.module_url('verification/email/key/'.$_GET['key'].'/', true).'" method="post">';
	echo '<table class="login-form">';
		echo '<tr>';
			echo '<td>Username:</td>';
			echo '<td><input type="text" name="acc_username" maxlength="25" autofocus/></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td>';
				echo '<div style="width: 300px;font-size: 12px;">';
					echo '<a href="'.module_url('recovery/username/', true).'">Forgot your username ?</a><br /><br />';
				echo '</div>';
			echo '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td></td>';
			echo '<td><button type="submit" name="acc_submit" value="ok">Continue</button></td>';
		echo '</tr>';
	echo '</table>';
	echo '</form>';
	
}
?>

