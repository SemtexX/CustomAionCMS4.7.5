<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */
?>
<?php if(isLoggedIn()) redirect('usercp/'); ?>
<div class="page-header-block accrecovery"></div>
<br /><br />

<h3>Forgot Password</h3>
<p>Please contact a GM on our Discord Server.</p>
<!--<p>Please enter the email address you used when creating your account.</p>
<p>We will send you a link to reset your password.</p>-->

<br />

<?php

if(check($_POST['lostpwd_submit'], $_POST['lostpwd_email'])) {
	try {
		
		if(!Validator::Email($_POST['lostpwd_email'])) throw new Exception('The email address you entered is not valid.');
		
		$Account = new Account();
		$Account->setEmail($_POST['lostpwd_email']);
		$Account->loadAccountDataFromEmail();
		$accountData = $Account->getAccountData();
		
		if(!is_array($accountData)) throw new Exception('The email address you entered is not associated with any Gamez Aion account.');
		
		# check if has an active password change
		/*if(check($accountData['hash'], $accountData['confirmed'])) {
			
			if(($accountData['hash']+3600) > time()) {
				throw new Exception('Your request could not be completed, please contact support. [2]');
			}
		}*/
		
		//$verificationCode = time();
		//$verificationLink = __PAGE_URL__ . 'verification/forgotpassword/key/' . $verificationCode . '/';
		
		# send recovery email
		/*try {
			
			$email = new Email();
			$email->setTemplate('RECOVER_PWD');
			$email->addVariable('{USERNAME}', $accountData['name']);
			$email->addVariable('{VERIFICATION_LINK}', $verificationLink);
			$email->addAddress($accountData['email']);
			$email->send();
			
		} catch(Exception $ex) {
			throw new Exception('We could not send you the verification email, please contact support.');
		}*/
		
		# update account data
		$setPasswordChange = $Account->setPasswordChange('passwordrecovery', $verificationCode);
		if(!$setPasswordChange) throw new Exception('There was a problem changing your password, please contact support. [3]');
		
		message('We sent you a verification link to <strong>'.$accountData['email'].'</strong>, please follow the instructions given in the email.', 'success');
		logSystem::add('requested password recovery ('.$accountData['email'].')');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
		logSystem::add('requested password recovery error', 5);
	}
}

?>

<form action="<?php module_url('recovery/password/'); ?>" method="post">
<table class="login-form">
	<tr>
		<td>Discord:</td>
		<td><!--<input type="text" name="lostpwd_email" autofocus/>-->
				<a href="https://discord.gg/4nwpdqJ4MM" target="_blank">Join our Server</a><br /><br /></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<div style="width: 300px;font-size: 12px;">
				<!--<a href="https://aioncms.com/" target="_blank">Forgot your email address ?</a><br /><br />-->
				
			</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<!--<td><button type="submit" name="lostpwd_submit" value="ok">Confirm</button></td>-->
	</tr>
</table>
</form>