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

<h3>Forgot Username</h3>
<p>Please contact a GM on our Discord Server.</p>
<!--<p>Please enter the email address you used when creating your account.</p>
<p>We will send you a link to reset your password.</p>-->

<br />

<?php

if(check($_POST['lostuser_submit'], $_POST['lostuser_email'])) {
	try {
		
		if(!Validator::Email($_POST['lostuser_email'])) throw new Exception('The email address you entered is not valid.');
		
		$Account = new Account();
		$Account->setEmail($_POST['lostuser_email']);
		$Account->loadAccountDataFromEmail();
		$accountData = $Account->getAccountData();
		
		if(!is_array($accountData)) throw new Exception('Your request could not be completed, please contact support. [1]');
		
		# send username reminder
		try {
			
			$email = new Email();
			$email->setTemplate('RECOVER_USERNAME');
			$email->addVariable('{USERNAME}', $accountData['name']);
			$email->addAddress($accountData['email']);
			$email->send();
			
		} catch(Exception $ex) {
			throw new Exception('We could not send you the username recovery email, please contact support.');
		}

		message('We sent your username to <strong>'.$accountData['email'].'</strong>, please check your email inbox.', 'success');
		logSystem::add('requested username recovery ('.$accountData['email'].')');
		
	} catch(Exception $ex) {
		message($ex->getMessage(), 'error');
		logSystem::add('requested username recovery error', 5);
	}
}

?>

<form action="<?php module_url('recovery/username/'); ?>" method="post">
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